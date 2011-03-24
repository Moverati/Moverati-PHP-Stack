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

namespace Core\Engine\View\Helper;

use Core\Engine,
    Core\Engine\View,
    Core\Engine\Template;

/**
 * TemplateUrl
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateUrl extends HelperAbstract
{
    /**
     * Template Action Helper
     *
     * @var Template\Controller\Action\Helper\Template
     */
    private $templateActionHelper;

    /**
     * Server URL
     *
     * @var string
     */
    private $serverUrl;

    /**
     * Template url
     *
     * @param $path Path to append to the template path
     * @return s
     */
    public function __invoke($path = null)
    {
        $serverUrl = $this->getServerUrl();
        return $serverUrl . $this->getTemplateUrl($path);
    }

    /**
     * Set layout action helper
     *
     * @param  Template\Controller\Action\Helper\Template $templateActionHelper
     * @return TemplateUrl
     */
    public function setTemplateActionHelper(Template\Controller\Action\Helper\TemplateManager $templateActionHelper)
    {
        $this->templateActionHelper = $templateActionHelper;
        return $this;
    }

    /**
     * Retrieve layout action helper
     *
     * @return Template\Controller\Action\Helper\TemplateManager
     */
    public function getTemplateActionHelper()
    {
        if (! $this->templateActionHelper instanceof Template\Controller\Action\Helper\TemplateManager) {
            $helper = \Zend_Controller_Action_HelperBroker::getStaticHelper('templateManager');

            $this->setTemplateActionHelper($helper);
        }

        return $this->templateActionHelper;
    }

    /**
     * Get the server url
     *
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * Set the server url
     *
     * @param string $serverUrl
     * @return TemplateUrl
     */
    public function setServerUrl($serverUrl)
    {
        $this->serverUrl = rtrim($serverUrl, '\//');
        return $this;
    }

    /**
     * Get the template url
     *
     * @param $path
     * @return string
     */
    private function getTemplateUrl($path = null)
    {
        $helper = $this->getTemplateActionHelper();
        $view   = $this->getView();
        
        return $view->baseUrl($helper->getActiveTemplatePublicPath() . $path);
    }
}