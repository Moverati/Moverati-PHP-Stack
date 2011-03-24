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

namespace Core\Engine\Controller\Action;

use Core\Engine\Controller\Plugin;

/**
 * Error Controller
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
abstract class ErrorAction extends \Zend_Controller_Action
{
    /**
     * Error handler action
     *
     */
    const ACTION     = 'action';

    /**
     * Error handler controller
     *
     */
    const CONTROLLER = 'controller';

    /**
     * Error handler module
     *
     */
    const MODULE     = 'module';

    /**
     * Error handler params
     *
     */
    const PARAMS     = 'params';

    /**
     * Exception Object
     *
     * $error->type (\Zend_Controller_PluginerrorHandler constants)
     * $error->exception (Exception object)
     *
     * @var Plugin\ErrorHandler
     */
    private $error;

    /**
     * Error handler map
     *
     * @var array
     */
    private $errorHandlers = array();

    /**
     * Fall back map
     *
     * @var array
     */
    private $fallBack = array();

    /**
     * No fall back flag
     *
     * @var boolean
     */
    private $noFallBack;

    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param \Zend_Controller_Request_Abstract $request
     * @param \Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        // Set error
        $error = $request->getParam(Plugin\ErrorHandler::ERROR_PARAM);
        if ($error instanceof Plugin\ErrorHandler\Data) {
            $this->setError($error);
        }

        // Setup default fallback
        $defaultModule = $this->getFrontController()->getDefaultModule();
        $this->setFallBack('error', 'error', $defaultModule);

        // Error Handling map
        $this->setErrorHandlers(array(
            Plugin\ErrorHandler::EXCEPTION_NO_CONTROLLER => array(
                self::ACTION     => 'not-found',
                self::CONTROLLER => 'error',
                self::MODULE     => $defaultModule
            ),
            Plugin\ErrorHandler::EXCEPTION_NO_ACTION     => array(
                self::ACTION     => 'not-found',
                self::CONTROLLER => 'error',
                self::MODULE     => $defaultModule
            ),

            Plugin\ErrorHandler::EXCEPTION_OTHER         => array(
                self::ACTION     => 'internal',
                self::CONTROLLER => 'error',
                self::MODULE     => $defaultModule
            )
        ));

        // Call Parent
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Error handler
     *
     * This is the entrance to this controller used by the ErrorHandler
     * controller plugin (@see \Zend_ControllererrorHandler)
     *
     * This action cannot be called directly, if someone does, it will
     * show up as a 404 notFound
     *
     * The magic of this controller happens in this action. Make sure that
     * the ErrorHandler is set to forward to this action.
     *
     * @return void
     */
    public function errorAction()
    {
        $error = $this->getError();
        if (!$error instanceof Plugin\ErrorHandler\Data) {
            // Reserve this action only for the ErrorHandler plugin
            throw new \Zend_Controller_Action_Exception(
                'This action cannot be called directly'
            );
        }

        $type              = $error->getType();
        $errorHandlers     = $this->getErrorHandlers();
        $currentModule     = $this->getRequest()->getModuleName();
        $currentController = $this->getRequest()->getControllerName();
        $fallBack          = $this->getFallBack();

        // Prevent looping to the same place
        if (strcasecmp($fallBack[self::MODULE], $currentModule) === 0 || $fallBack[self::MODULE] === null) {
            $isValidFall = !(strcasecmp($fallBack[self::CONTROLLER], $currentController) === 0
                                && $fallBack[self::ACTION] == 'error');
        } else {
            $isValidFall = true;
        }

        if (isset($errorHandlers[$type])) {
            call_user_func_array(array($this, '_forward'), $errorHandlers[$type]);
        } else if (!$this->canFallBack() && $isValidFall) {
            call_user_func_array(array($this, '_forward'), $fallBack);
        } else if (!$this->canFallBack() && !$isValidFall) {
            throw new \Zend_Controller_Action_Exception(
                sprintf('An exception of type "%s" occurred and was not handled by "%s" '
                         . 'because falling back would of caused a loop', $type, get_class($this)));
        } else {
            throw new \Zend_Controller_Action_Exception(
                sprintf('An exception of type "%s" occurred and was not handled by "%s"', $type, get_class($this)));
        }

        if (!$this->getInvokeArg('noViewRenderer') && $this->_helper->hasHelper('ViewRenderer')) {
            // Disable ViewRenderer
            $this->getHelper('ViewRenderer')->setNoRender();
        }

        // Clear header/body
        $this->getResponse()->clearHeaders()
                            ->clearBody();
    }

    /**
     * Set error
     *
     * @param Plugin\ErrorHandler\Data $error
     * @return ErrorAction
     */
    public function setError(Plugin\ErrorHandler\Data $error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Get error obj
     *
     * @return Plugin\ErrorHandler\Data
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Clear and set error handlers
     *
     * @param array $array
     * @return ErrorAction
     */
    public function setErrorHandlers(array $array = array())
    {
        // Clear handlers
        $this->errorHandlers = array();

        // Add them from array
        foreach ($array as $type => $options) {
            $action     = is_array($options) && isset($options[self::ACTION])
                            ? $options[self::ACTION] : (is_string($options) ? $options : null);

            $controller = is_array($options) && isset($options[self::CONTROLLER])
                            ? $options[self::CONTROLLER] : null;

            $module     = is_array($options) && isset($options[self::MODULE])
                            ? $options[self::MODULE] : null;

            $params     = is_array($options) && isset($options[self::PARAMS])
                            ? $options[self::PARAMS] : array();

            $this->addErrorHandler($type, $action, $controller, $module, $params);
        }

        return $this;
    }

    /**
     * Add error handlers
     *
     * Error handlers are added in FIFO order
     *
     * @param string $type
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return ErrorAction
     */
    public function addErrorHandler($type, $action, $controller = null, $module = null, array $params = array())
    {
        $this->errorHandlers[$type] = array(
            self::ACTION     => $action,
            self::CONTROLLER => $controller,
            self::MODULE     => $module,
            self::PARAMS     => $params
        );

        return $this;
    }

    /**
     * Get array of error handlers
     *
     * @return array
     */
    public function getErrorHandlers()
    {
        return $this->errorHandlers;
    }

    /**
     * Set to prevent fall back
     *
     * @param boolean $fall
     *
     * @return ErrorAction
     */
    public function setNoFallBack($fall = true)
    {
        $this->noFallBack = (bool) $fall;
        return $this;
    }

    /**
     * Get no fall back flag
     *
     * @return boolean
     */
    public function canFallBack()
    {
        return (bool) $this->noFallBack;
    }

    /**
     * Set fall back action
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array  $params
     *
     * @return ErrorAction
     */
    public function setFallBack($action, $controller = null, $module = null, array $params = array())
    {
        $this->fallBack = array(
            self::ACTION     => $action,
            self::CONTROLLER => $controller,
            self::MODULE     => $module,
            self::PARAMS     => $params
        );

        return $this;
    }

    /**
     * Get fallback
     *
     * @return array
     */
    public function getFallBack()
    {
        return $this->fallBack;
    }
}