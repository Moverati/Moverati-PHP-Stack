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

namespace Core\Engine\Controller\Action\Helper;

/**
 * Helper abstract test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class HelperAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNameNamespaces()
    {
        $helperAbstract = new TestHelper();
        $this->assertEquals('TestHelper', $helperAbstract->getName());

        $globalClass = str_replace('\\', '', get_class($this));
        eval('class ' . $globalClass . ' extends Core\Engine\Controller\Action\Helper\HelperAbstract {}');

        $helperAbstract = new $globalClass();
        $this->assertEquals($globalClass, $helperAbstract->getName());
    }
    public function testGetNameUnderscores()
    {
        $helperAbstract = new \Core_Engine_Controller_Action_Helper_TestUnderscores();
        $this->assertEquals('TestUnderscores', $helperAbstract->getName());
    }
}