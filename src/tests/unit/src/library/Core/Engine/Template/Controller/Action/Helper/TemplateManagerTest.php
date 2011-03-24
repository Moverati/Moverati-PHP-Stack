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

/**
 * SeleniumTestCase test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $tm           = new \Core\Engine\Template\TemplateManager();
        $this->object = new TemplateManager($tm);
    }

    public function testInit()
    {
        $this->object->postDispatch();
        $this->assertTrue($this->object->isActionControllerSuccessful());
        $this->object->init();
        $this->assertFalse($this->object->isActionControllerSuccessful());
    }

    public function testSetAndGetTemplateManager()
    {
        $tm2 = new \Core\Engine\Template\TemplateManager();
        $this->assertNotSame($tm2, $this->object->getTemplateManager());
        $this->object->setTemplateManager($tm2);
        $this->assertSame($tm2, $this->object->getTemplateManager());
    }

    public function testDirect()
    {
        $tm = $this->object->direct();
        $this->assertSame($tm, $this->object->getTemplateManager());
    }

    public function test__callWithValidMethod()
    {
        $this->assertFalse($this->object->getMvcEnabled());
    }

    public function test__callWithInValidMethod()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $this->object->dontExistsMofo();
    }
}