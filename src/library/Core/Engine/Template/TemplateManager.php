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

namespace Core\Engine\Template;

use Core\Engine\Template\Controller\Plugin,
    Core\Engine\Template\Controller\Action\Helper;

/**
 * Template
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateManager
{
    /**
     * Flag: is MVC integration enabled?
     *
     * @var bool
     */
    protected $mvcEnabled = true;

    /**
     * Instance registered with MVC, if any
     *
     * @var TemplateManager
     */
    protected static $mvcInstance;

    /**
     * Flag: is MVC successful action only flag set?
     *
     * @var bool
     */
    protected $mvcSuccessfulActionOnly = true;

    /**
     * Helper class
     * @var string
     */
    private $helperClass = 'Core\Engine\Template\Controller\Action\Helper\TemplateManager';

    /**
     * Plugin class
     * @var string
     */
    private $pluginClass = 'Core\Engine\Template\Controller\Plugin\TemplatePlugin';

    /**
     * Template paths
     *
     * @var array
     */
    private $templates = array();

    /**
     * Current template
     *
     * @var string
     */
    private $activeTemplate = 'default';

   /**
     * Constructor
     *
     * Layout script path, either as argument or as key in options, is
     * required.
     *
     * If mvcEnabled flag is false from options, simply sets layout script path.
     * Otherwise, also instantiates and registers action helper and controller
     * plugin.
     *
     * @param  string|array|Zend_Config $options
     * @return void
     */
    public function __construct(array $options = array(), $initMvc = false)
    {
        $this->setOptions($options);

        if ($initMvc) {
            $this->setMvcEnabled(true);
            $this->initMvc();
        } else {
            $this->setMvcEnabled(false);
        }
    }

    /**
     * Static method for initialization with MVC support
     *
     * @param  string|array|Zend_Config $options
     * @return TemplateManager
     */
    public static function startMvc(array $options = array())
    {
        if (null === static::$mvcInstance) {
            static::$mvcInstance = new self($options, true);
        }

        static::$mvcInstance->setOptions($options);

        return static::$mvcInstance;
    }

    /**
     * Retrieve MVC instance of Template object
     *
     * @return TemplateManager
     */
    public static function getMvcInstance()
    {
        return static::$mvcInstance;
    }

    /**
     * Reset MVC instance
     *
     * Unregisters plugins and helpers, and destroys MVC layout instance.
     *
     * @return void
     */
    public static function resetMvcInstance()
    {
        if (null !== self::$mvcInstance) {
            $template      = static::$mvcInstance;
            $pluginClass   = $template->getPluginClass();
            $front         = \Zend_Controller_Front::getInstance();

            if ($front->hasPlugin($pluginClass)) {
                $front->unregisterPlugin($pluginClass);
            }

            if (\Zend_Controller_Action_HelperBroker::hasHelper('templateManager')) {
                \Zend_Controller_Action_HelperBroker::removeHelper('templateManager');
            }

            unset($template);
            static::$mvcInstance = null;
        }
    }

    /**
     * Set options en masse
     *
     * @param  array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else if (method_exists($this, 'add' . ucfirst($key))) {
                $this->{'add' . ucfirst($key)}($value);
            }
        }
    }

    /**
     * Retrieve MVC enabled flag
     *
     * @return bool
     */
    public function getMvcEnabled()
    {
        return $this->mvcEnabled;
    }

    /**
     * Set MVC Successful Action Only flag
     *
     * @param bool $successfulActionOnly
     * @return TemplateManager
     */
    public function setMvcSuccessfulActionOnly($successfulActionOnly)
    {
        $this->mvcSuccessfulActionOnly = (bool) ($successfulActionOnly);
        return $this;
    }

    /**
     * Get MVC Successful Action Only Flag
     *
     * @return bool
     */
    public function getMvcSuccessfulActionOnly()
    {
        return $this->mvcSuccessfulActionOnly;
    }

    /**
     * Set the active template
     *
     * @param $name
     * @return TemplateManager
     */
    public function setActiveTemplate($name)
    {
        if (! $this->hasTemplate($name)) {
            throw new Exception(sprintf(
            	'Template "%s" could not be found', $name
            ));
        }

        $this->activeTemplate = $name;

        return $this;
    }

    /**
     * Get the active template
     *
     * @return string
     */
    public function getActiveTemplate()
    {
        return $this->activeTemplate;
    }

    /**
     * Get the active template path
     *
     * @return string
     */
    public function getActiveTemplatePath()
    {
        $name     = $this->getActiveTemplate();
        $template = $this->getTemplate($name);
        return isset($template['path']) ? $template['path'] : null;
    }

    /**
     * Get the active template public path
     *
     * @return string
     */
    public function getActiveTemplatePublicPath()
    {
        $name     = $this->getActiveTemplate();
        $template = $this->getTemplate($name);
        return isset($template['publicPath']) ? $template['publicPath'] : null;
    }

    /**
     * Specify a directory as containing templates
     *
     * Iterates through the directory, adding any subdirectories as templates;
     * the subdirectory within each template named after
     * will be used as the controller directory path.
     *
     * @param  string $path       Path to templates
     * @param  string $publicPath Path to be accessible from the web
     * @return \Zend_Controller_Front
     */
    public function addTemplatesPath($path, $publicPath = 'templates/')
    {
        if (is_array($path)) {
            foreach($path as $p){
                if (is_array($p) && isset($p['path']) && isset($p['publicPath'])) {
                    $this->addTemplatesPath((string) $p['path'], $p['publicPath']);
                } else if (is_array($p) && isset($p['path'])) {
                    $this->addTemplatesPath((string) $p['path']);
                } else {
                    $pathConversion = array_shift($path);
                    if (is_string($pathConversion)) {
                        $this->addTemplatesPath($pathConversion);
                    } else {
                        throw new Exception("Could not determine valid path");
                    }
                }
            }

            return $this;
        }

        try {
            $dir = new \DirectoryIterator($path);
        } catch (\Exception $e) {
            throw new Exception("Directory $path not readable");
        }

        foreach ($dir as $file) {
            if ($file->isDot() || !$file->isDir()) {
                continue;
            }

            $template    = $file->getFilename();

            // Don't use SCCS directories as modules
            if (preg_match('/^[^a-z]/i', $template) || ('CVS' == $template)) {
                continue;
            }

            $templateDir = $file->getPathname();
            $this->addTemplate($templateDir, $template, rtrim($publicPath, '/\\') . '/' . $template);
        }

        return $this;
    }

    /**
     * Add a template
     *
     * Overrides existing templates
     *
     * @param $path       Path to template
     * @param $name       Template Name
     * @param $publicPath Public path
     * @return Template
     */
    public function addTemplate($path, $name = null, $publicPath = null)
    {
        if (! is_dir($path)) {
            throw new Exception(sprintf(
            	'Template path "%s" does not appear to be a valid directory', $path
            ));
        }

        if ($name === null) {
            $name = pathinfo($path, \PATHINFO_BASENAME);
        }

        if ($publicPath === null) {
            $publicPath = 'templates/' . $name;
        }

        $this->templates[strtolower($name)] = array(
            'path'       => rtrim($path, '\//'),
            'publicPath' => rtrim($publicPath, '/\\')
        );

        return $this;
    }

    /**
     * Get a template info
     *
     * @param $name
     * @return array
     */
    public function getTemplate($name)
    {
        if (! $this->hasTemplate($name)) {
            throw new Exception(sprintf(
                'Template "%s" does not exist', $name
            ));
        }

        return $this->templates[strtolower($name)];
    }

    /**
     * Whether or not a template exists
     *
     * @param $name
     * @return boolean
     */
    public function hasTemplate($name)
    {
        return array_key_exists(strtolower($name), $this->templates);
    }

    /**
     * Set the templates
     *
     * @param array $templates
     * @return Template
     */
    public function setTemplates(array $templates)
    {
        $this->templates = $templates;
        return $this;
    }

    /**
     * Get templates
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }
    /**
     * Retrieve helper class
     *
     * @return string
     */
    public function getHelperClass()
    {
        return $this->helperClass;
    }

    /**
     * Set helper class
     *
     * @param  string $helperClass
     * @return Template
     */
    public function setHelperClass($helperClass)
    {
        $this->helperClass = (string) $helperClass;
        return $this;
    }

    /**
     * Retrieve plugin class
     *
     * @return string
     */
    public function getPluginClass()
    {
        return $this->pluginClass;
    }

    /**
     * Set plugin class
     *
     * @param  string $pluginClass
     * @return Template
     */
    public function setPluginClass($pluginClass)
    {
        $this->pluginClass = (string) $pluginClass;
        return $this;
    }

    /**
     * Initialize MVC integration
     *
     * @return void
     */
    protected function initMvc()
    {
        $helper = $this->initHelper();
        $this->initPlugin($helper);
    }

    /**
     * Initialize front controller plugin
     *
     * @param Helper\Template $helper;
     * @return Plugin\Template
     */
    protected function initPlugin(Helper\TemplateManager $helper)
    {
        $pluginClass = $this->getPluginClass();
        $front       = \Zend_Controller_Front::getInstance();

        $plugin      = new $pluginClass($this, $helper);
        if (! $front->hasPlugin($pluginClass)) {
            $front->registerPlugin(
                // register to run last | BUT before the ErrorHandler (if its available)
                $plugin,
                98
            );
        }

        return $plugin;
    }

    /**
     * Initialize action helper
     *
     * @return Helper\Template
     */
    protected function initHelper()
    {
        $helperClass = $this->getHelperClass();
        $helper      = new $helperClass($this);
        
        if (! \Zend_Controller_Action_HelperBroker::hasHelper('templateManager')) {
             \Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-95, $helper);
        }

        return $helper;
    }

    /**
     * Set MVC enabled flag
     *
     * @param  bool $mvcEnabled
     * @return Template
     */
    protected function setMvcEnabled($mvcEnabled)
    {
        $this->mvcEnabled = (bool) $mvcEnabled;
        return $this;
    }
}