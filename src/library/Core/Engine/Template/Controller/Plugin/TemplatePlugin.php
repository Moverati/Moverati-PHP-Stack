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

namespace Core\Engine\Template\Controller\Plugin;

use Core\Engine\Template,
    Core\Engine\Template\Controller\Action\Helper;

/**
 * Template Plugin
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplatePlugin extends \Zend_Controller_Plugin_Abstract
{
    /**
     * Template Action Helper
     *
     * @var Helper\Template
     */
    protected $templateActionHelper;

    /**
     * Template object
     *
     * @var Template\TemplateManager
     */
    protected $templateManager;

    /**
     * Constructor
     *
     * @param  Template\TemplateManager $templateManager
     * @param  Helper\Template          $templateActionHelper
     * @return void
     */
    public function __construct(Template\TemplateManager $templateManager, Helper\TemplateManager $templateActionHelper)
    {
        $this->setTemplateManager($templateManager);
        $this->setTemplateActionHelper($templateActionHelper);
    }

    /**
     * Retrieve the template object
     *
     * @return Template\TemplateManager
     */
    public function getTemplateManager()
    {
        return $this->templateManager;
    }

    /**
     * Set the template object
     *
     * @param  Template\TemplateManager $templateManager
     * @return Template
     */
    public function setTemplateManager(Template\TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
        return $this;
    }

    /**
     * Set layout action helper
     *
     * @param  Helper\Template $templateActionHelper
     * @return Template
     */
    public function setTemplateActionHelper(Helper\TemplateManager $templateActionHelper)
    {
        $this->templateActionHelper = $templateActionHelper;
        return $this;
    }

    /**
     * Retrieve layout action helper
     *
     * @return Helper\Template
     */
    public function getTemplateActionHelper()
    {
        return $this->templateActionHelper;
    }

    /**
     * postDispatch() plugin hook -- render layout
     *
     * @param \Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $template = $this->getTemplateManager();
        $helper   = $this->getTemplateActionHelper();

        // Return early if forward detected
        if (!$request->isDispatched()
            || ($template->getMvcSuccessfulActionOnly()
                && (!empty($helper) && !$helper->isActionControllerSuccessful())))
        {
            return;
        }

        $layout = \Zend_Layout::getMvcInstance();
        $layout->setLayoutPath($template->getActiveTemplatePath() . '/layouts/');
        return;
    }
}