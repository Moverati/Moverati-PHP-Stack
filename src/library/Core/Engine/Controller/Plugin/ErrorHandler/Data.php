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

namespace Core\Engine\Controller\Plugin\ErrorHandler;

/**
 * ErrorHandler Data
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Data
{
    /**
     * Exception
     *
     * @var \Exception
     */
    protected $exception;

    /**
     * Request that cause the error
     *
     * @var \Zend_Controller_Request_Abstract
     */
    protected $request;

    /**
     * Exception type
     *
     * @var string
     */
    protected $type;

    /**
     * Construct
     *
     * @param string $type
     * @param \Exception $exception
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function __construct($type, \Exception $exception, \Zend_Controller_Request_Abstract $request)
    {
        $this->type      = $type;
        $this->exception = $exception;
        $this->request   = $request;
    }

    /**
     * Get exception
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Get request object which caused error
     *
     * @return \Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get request type defined by errorHandler
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get
     *
     * Provide backwords compatibility
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (in_array($key, array('exception', 'request', 'type'))) {
            return $this->{$key};
        }
    }

    /**
     * Isset
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        if (in_array($key, array('exception', 'request', 'type'))) {
            return isset($this->{$key});
        }
    }
}