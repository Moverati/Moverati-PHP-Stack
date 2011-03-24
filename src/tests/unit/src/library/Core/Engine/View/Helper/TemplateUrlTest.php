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

use Core\Engine\Template\Controller\Action\Helper;

/**
 * Template url
 *
 * @author    Josh Team
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateUrl
     */
    protected $object;

    private $templateDir;
    private $templateName;
    private $templatePub;

    protected function setUp()
    {
        $view         = new \Zend_View();
        $this->object = new TemplateUrl;

        $this->templateDir  = '.';
        $this->templateName = 'activeTest';
        $this->templatePub  = '/templates';

        $tm   = new \Core\Engine\Template\TemplateManager();
        $tm->addTemplate($this->templateDir, $this->templateName, $this->templatePub);
        $tm->setActiveTemplate($this->templateName);

        $this->object->setView($view);
        $this->object->setTemplateActionHelper(new \Core\Engine\Template\Controller\Action\Helper\TemplateManager($tm));
    }

    protected function tearDown()
    {
        \Zend_Controller_Action_HelperBroker::removeHelper('templateManager');
    }
    
    public function testCallingAsFunction()
    {
        $class = $this->object;
        $this->assertEquals($this->templatePub, $class());
    }

    public function testCallingAsFunctionWithPath()
    {
        $class = $this->object;
        $path  = '/foo';
        $this->assertEquals($this->templatePub . $path, $class($path));
    }

    public function testSetAndGetTemplateActionHelper()
    {
        $tm              = new \Core\Engine\Template\TemplateManager();
        $templateManager = new \Core\Engine\Template\Controller\Action\Helper\TemplateManager($tm);
        $this->object->setTemplateActionHelper($templateManager);
        $this->assertEquals($templateManager, $this->object->getTemplateActionHelper());
    }

    public function testGetTemplateActionHelperShouldThrowException()
    {
        $this->setExpectedException('Zend_Controller_Action_Exception');
        $templateUrl = new TemplateUrl();
        $templateUrl->getTemplateActionHelper();
    }

    public function testGetTemplateActionHelperShouldLoadActionHelper()
    {
        $helper = new Helper\TemplateManager(new \Core\Engine\Template\TemplateManager());
        \Zend_Controller_Action_HelperBroker::addHelper($helper);
        $templateUrl = new TemplateUrl();
        $this->assertSame($helper, $templateUrl->getTemplateActionHelper());
    }
}