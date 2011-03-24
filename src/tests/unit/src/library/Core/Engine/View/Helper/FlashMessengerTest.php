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

/**
 * FlashMessenger
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FlashMessengerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlashMessenger
     */
    protected $object;


    protected function setUp()
    {
        $view         = new \Zend_View();
        $this->object = new FlashMessenger();
        \Zend_Session::$_unitTestEnabled = true;
    }

    protected function tearDown()
    {
        \Zend_Controller_Action_HelperBroker::removeHelper('FlashMessenger');
    }

    public function testFlashMessenger()
    {
        $messenger = $this->object->__invoke();
        $this->assertType('Zend_Controller_Action_Helper_FlashMessenger', $messenger);
    }

    public function testFlashMessengerSetsNamespace()
    {
        $messenger = $this->object->__invoke('test');
        $this->assertType('Zend_Controller_Action_Helper_FlashMessenger', $messenger);

        $r = new \ReflectionObject($messenger);
        $p = $r->getProperty('_namespace');
        $p->setAccessible(true);

        $namespace = $p->getValue($messenger);

        $this->assertEquals('test', $namespace);
    }
}