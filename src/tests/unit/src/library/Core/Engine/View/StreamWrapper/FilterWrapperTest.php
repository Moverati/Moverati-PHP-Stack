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

namespace Core\Engine\View\StreamWrapper;

use Core\Engine,
    Core\Engine\View;

/**
 * ViewAbstract Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FilterWrapperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $view = new Engine\View();
        FilterWrapper::setView($view);
    }

    public function testStreamOpen()
    {
        $wrapper    = new FilterWrapper();
        $openedPath = '';

        $result  = $wrapper->stream_open(__FILE__, 'r', null, $openedPath);
        $this->assertTrue($result);

        $result  = $wrapper->stream_open(__FILE__ . 'NonExistentFile', 'r', null, $openedPath);
        $this->assertFalse($result);
    }
}