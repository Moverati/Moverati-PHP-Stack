<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\View;

/**
 * ViewAbstract
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
abstract class ViewAbstract extends \Zend_View_Abstract
{
    /**
     * Stack of Zend_View_Filter names to apply as stream filters.
     *
     * @var array
     */
    private $streamFilter = array();

    /**
     * Flag to indicate whether to use streams
     *
     * @var boolean
     */
    private $streamFlag   = true;

    /**
     * Stream protocol to use
     *
     * @var string
     */
    private $streamProtocol = 'view';

    /**
     * PHP stream wrapper for views
     *
     * @var string
     */
    private $streamWrapper = 'Core\Engine\View\StreamWrapper\FilterWrapper';

    /**
     * Constructor.
     *
     * @param array $config Configuration key-value pairs.
     */
    public function __construct(array $config = array())
    {
        // Disable streams
        if (array_key_exists('streamFlag', $config)) {
            $this->setStreamFlag($config['streamFlag']);
        }

        // Stream protocol
        if (array_key_exists('streamProtocol', $config)) {
            $this->setStreamProtocol($config['streamProtocol']);
        }

        // Stream wrapper
        if (array_key_exists('streamWrapper', $config)) {
            $this->setStreamWrapper($config['streamWrapper']);
        }

        // User-defined stream filters
        if (array_key_exists('streamFilter', $config)) {
            $this->addStreamFilter($config['streamFilter']);
        }

        // Call parent
        parent::__construct($config);
    }

    /**
     * Accesses a helper object from within a script.
     *
     * If the helper class has a 'view' property, sets it with the current view
     * object.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        // is the helper already loaded?
        $helper = $this->getHelper($name);

        // call the helper method
        $name = method_exists($helper, '__invoke') ? '__invoke' : $name;
        return call_user_func_array(
            array($helper, $name),
            $args
        );
    }

    /**
     * Return array of all currently active filters
     *
     * Returns array of strings if filters have not been
     * instantiated
     *
     * @return array
     */
    public function getStreamFilters()
    {
        return $this->streamFilter;
    }

    /**
     * Add one or more stream filters to the stack in FIFO order.
     *
     * @param string|array One or more filters to add.
     * @return Zym_View_Abstract
     */
    public function addStreamFilter($name)
    {
        foreach ((array) $name as $val) {
            $this->streamFilter[] = $val;
        }

        return $this;
    }

    /**
     * Resets the stream filter stack.
     *
     * To clear all filters, use Zend_View::setFilter(null).
     *
     * @param string|array One or more filters to set.
     * @return Zym_View_Abstract
     */
    public function setStreamFilter($name)
    {
        $this->streamFilter = array();
        $this->addStreamFilter($name);

        return $this;
    }

    /**
     * Set stream flag
     *
     * Whether to disable or enable use of stream wrappers
     *
     * @param boolean $flag
     * @return Zym_View_Abstract
     */
    public function setStreamFlag($flag)
    {
        $this->streamFlag = (bool) $flag;

        return $this;
    }

    /**
     * Get stream flag
     *
     * Whether streams are enabled or not
     *
     * @return boolean
     */
    public function getStreamFlag()
    {
        return $this->streamFlag;
    }

    /**
     * Set view stream wrapper class
     *
     * Protocol must be alphanumeric
     *
     * @param string $protocol
     * @return Zym_View_Abstract
     */
    public function setStreamProtocol($protocol)
    {
        if (empty($protocol)) {
            throw new Exception(
                'Stream protocol "' . $protocol . '" cannot be empty'
            );
        }

        $this->streamProtocol= (string) $protocol;

        return $this;
    }

    /**
     * Get stream protocol
     *
     * @return string
     */
    public function getStreamProtocol()
    {
        return $this->streamProtocol;
    }

    /**
     * Set view stream wrapper class
     *
     * @param string $class
     * @return Zym_View_Abstract
     */
    public function setStreamWrapper($class)
    {
        $this->streamWrapper = (string) $class;

        return $this;
    }

    /**
     * Get stream wrapper class
     *
     * @return string
     */
    public function getStreamWrapper()
    {
        return $this->streamWrapper;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        // Revert to no stream
        if (!$this->getStreamFlag()) {
            return parent::render($name);
        }

        // Get stream class
        $stream         = $this->getStreamWrapper();
        $streamProtocol = $this->getStreamProtocol();

        // Do extra work if something already registered our protocol
        $previousWrapperExists = false;

        // Unregister existing wrapper
        if (in_array($streamProtocol, stream_get_wrappers())) {
            stream_wrapper_unregister($streamProtocol);
            $previousWrapperExists = true;
        }

        // Load stream wrapper
        $this->_loadStreamWrapper($stream);

        // Register wrapper
        stream_wrapper_register($streamProtocol, $stream);

        // Render!
        $return = parent::render($name);

        

        // Register any old wrapper
        if ($previousWrapperExists) {
            @stream_wrapper_restore($streamProtocol);
        } else if (in_array($streamProtocol, stream_get_wrappers())) { // Unregister wrapper
            stream_wrapper_unregister($streamProtocol);
        }

        return $return;
    }

    /**
     * Retrieve plugin loader for a specific plugin type
     *
     * @param  string $type
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader($type)
    {
        $type   = strtolower($type);
        $loader = parent::getPluginLoader($type);

        // Add Zym Prefix
        $pType  = ucfirst($type);
        $prefix = 'Core\Engine\View\\' . $pType . '\\';
        $path   = 'Core/Engine/View/' . $pType;

        if (!$loader->getPaths($prefix)) {
            switch ($type) {
                case 'filter':
                case 'helper':
                default:
                    $loader->addPrefixPath($prefix, $path);
            }
        }

        return $loader;
    }

    /**
     * Load and setup stream wrapper
     *
     * @param string $stream
     */
    protected function _loadStreamWrapper($stream)
    {
        // Setup Wrapper
        call_user_func(array($stream, 'setView'), $this);
    }
}