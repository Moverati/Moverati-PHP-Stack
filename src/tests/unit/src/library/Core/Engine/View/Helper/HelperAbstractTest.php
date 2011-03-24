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
 * Helper abstract test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class HelperAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HelperAbstract
     */
    protected $object;
    
    protected function setUp()
    {
        $this->object = new \Core\Engine\View\Helper\Mock\Helper;
    }

    public function testGetViewNoViewSet()
    {
        $this->setExpectedException('Core\Engine\View\Helper\Exception');
        $this->object->getView();
    }

    public function testGetView()
    {
        $view = new \Zend_View();
        $this->object->setView($view);
        $this->assertEquals($view, $this->object->getView());
    }
    
}