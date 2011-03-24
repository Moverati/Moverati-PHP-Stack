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

/**
 * Template Plugin Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplatePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplatePlugin
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $tm           = new \Core\Engine\Template\TemplateManager();
        $tm->addTemplate('.', 'default', 'template');

        $helper       = new \Core\Engine\Template\Controller\Action\Helper\TemplateManager($tm);
        $this->object = new TemplatePlugin($tm, $helper);
    }

    public function testSetAndGetTemplateManager()
    {
        $tm2 = new \Core\Engine\Template\TemplateManager();
        $this->assertNotSame($tm2, $this->object->getTemplateManager());
        $this->object->setTemplateManager($tm2);
        $this->assertSame($tm2, $this->object->getTemplateManager());
    }

    public function testSetAndGetTemplateActionHelper()
    {
        $tm      = new \Core\Engine\Template\TemplateManager();
        $helper2 = new \Core\Engine\Template\Controller\Action\Helper\TemplateManager($tm);
        $this->assertNotSame($helper2, $this->object->getTemplateActionHelper());
        $this->object->setTemplateActionHelper($helper2);
        $this->assertSame($helper2, $this->object->getTemplateActionHelper());
    }

    public function testPostDispatchRequestNotDispatched()
    {
        \Zend_Layout::startMvc();
        $layout = \Zend_Layout::getMvcInstance();
        $this->assertNull($layout->getLayoutPath());

        $http = new \Zend_Controller_Request_Http();
        $this->object->postDispatch($http);
        $this->assertNull($layout->getLayoutPath());
    }

    public function testPostDispatchRequestDispatched()
    {
        \Zend_Layout::startMvc();
        $layout = \Zend_Layout::getMvcInstance();
        $this->assertNull($layout->getLayoutPath());

        $http = new \Zend_Controller_Request_Http();
        $http->setDispatched(true);

        $this->object->getTemplateActionHelper()->postDispatch();
        
        $this->object->postDispatch($http);
        $this->assertNotNull($layout->getLayoutPath());
    }
}