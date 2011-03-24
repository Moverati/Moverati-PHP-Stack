<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

use Core\Engine\Controller\Action;

/**
 * Error controller
 *
 * @author    Geoffrey Tran
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Default_ErrorController extends Action\ErrorAction
{
    /**
     * Init
     * 
     * @return void
     */
    public function init()
    {
        $this->view->assign(array(
            'serviceContainer' => $this->getHelper('ServiceContainer')
        ));
    }

    /**
     * Not-Found
     *
     * Action used when an action or controller could not be dispatched.
     * This is commonly referred to as an HTTP 404
     *
     * @return void
     */
    public function notFoundAction()
    {
        $errorService = new \Core\HalfPipe\Service\ErrorService();
        $errorType    = $errorService->handleError($this->getRequest());

        // Send 404 HTTP Error
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');

        $this->view->assign(array(
            'error'     => $this->getError(),
            'errorType' => $errorType
        ));
    }

    /**
     * Internal Error
     *
     * Action used when an internal server error occured.
     * Commonly referred to as an HTTP 500
     *
     * @return void
     */
    public function internalAction()
    {
        if ($error = $this->getError()) {
            $errorService = new \Core\HalfPipe\Service\ErrorService();
            $errorService->handleException($error->getException());
        }

        // Send 500 HTTP Error
        $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');

        $this->view->assign(array(
            'error' => $this->getError()
        ));
    }

    /**
     * Unauthorized access
     *
     * HTTP 401
     *
     * @return void
     */
    public function unauthorizedAction()
    {
        // Send 401 HTTP Error
        $this->getResponse()->setRawHeader('HTTP/1.1 401 Unauthorized');

        $this->view->assign(array(
            'error' => $this->getError()
        ));
    }

    /**
     * Forbidden access
     *
     * HTTP 403
     *
     * @return void
     */
    public function forbiddenAction()
    {
        // Send 403 HTTP Error
        $this->getResponse()->setRawHeader('HTTP/1.1 403 Forbidden');

        $this->view->assign(array(
            'error' => $this->getError()
        ));
    }
}
