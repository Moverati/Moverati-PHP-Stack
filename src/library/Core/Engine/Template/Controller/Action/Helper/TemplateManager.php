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

namespace Core\Engine\Template\Controller\Action\Helper;

use Core\Engine\Template,
    Core\Engine\Controller\Action\Helper;

/**
 * Template Helper
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateManager extends Helper\HelperAbstract
{
    /**
     * Template Manager
     *
     * @var Template\TemplateManager
     */
    private $templateManager;

    /**
     * Whether action was successful or not
     *
     * @var bool
     */
    protected $isActionControllerSuccessful = false;

    /**
     * Constructor
     *
     * @param  Template\TemplateManager $templateManager
     * @return void
     */
    public function __construct(Template\TemplateManager $templateManager)
    {
        $this->setTemplateManager($templateManager);
    }

    /**
     * Init
     */
    public function init()
    {
        $this->isActionControllerSuccessful = false;
    }


    /**
     * Get layout object
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
     * @return TemplateManager
     */
    public function setTemplateManager(Template\TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
        return $this;
    }

    /**
     * Mark Action Controller (according to this plugin) as Running successfully
     *
     * @return TemplateManager
     */
    public function postDispatch()
    {
        $this->isActionControllerSuccessful = true;
        return $this;
    }

    /**
     * Did the previous action successfully complete?
     *
     * @return bool
     */
    public function isActionControllerSuccessful()
    {
        return $this->isActionControllerSuccessful;
    }

    /**
     * Strategy pattern; call object as method
     *
     * @return Template\TemplateManager
     */
    public function direct()
    {
        return $this->getTemplateManager();
    }

    /**
     * Proxy method calls to template object
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $layout = $this->getTemplateManager();
        if (method_exists($layout, $method)) {
            return call_user_func_array(array($layout, $method), $args);
        }

        throw new Template\Exception(sprintf(
        	'Invalid method "%s" called on template action helper', $method
        ));
    }
}