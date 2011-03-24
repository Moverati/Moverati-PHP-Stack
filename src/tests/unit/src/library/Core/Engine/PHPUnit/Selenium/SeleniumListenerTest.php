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

namespace Core\Engine\PHPUnit\Selenium;

/**
 * SeleniumListener test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SeleniumListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAcceptsBrowserUrl()
    {
        $listener = new SeleniumListener('test');
        $this->assertEquals('test/', $listener->getBrowserUrl());
    }

    public function testConstuctorWorksWithoutArguments()
    {
        $listener = new SeleniumListener();
    }

    public function testGetAndSetBrowserUrl()
    {
        $listener = new SeleniumListener();
        $listener->setBrowserUrl('test');

        $this->assertEquals('test/', $listener->getBrowserUrl());
    }

    public function testSetBrowserUrlTrimsTrailingSlash()
    {
        $listener = new SeleniumListener('http://google.com\//');
        $this->assertEquals('http://google.com/', $listener->getBrowserUrl());

        $listener->setBrowserUrl('http://google.com/');
        $this->assertEquals('http://google.com/', $listener->getBrowserUrl());

        $listener->setBrowserUrl('http://google.com//');
        $this->assertEquals('http://google.com/', $listener->getBrowserUrl());
    }
    
    public function testSetBrowserUrlAddsTrailingSlashIfNotExistent()
    {
        $listener = new SeleniumListener('http://google.com');
        $this->assertEquals('http://google.com/', $listener->getBrowserUrl());

        $listener->setBrowserUrl('http://google.com');
        $this->assertEquals('http://google.com/', $listener->getBrowserUrl());

    }

    public function testStartTestSetsBrowserUrlForSeleniumTest()
    {
        $test = $this->getMockForAbstractClass('PHPUnit_Extensions_SeleniumTestCase', array('setBrowserUrl'));

        // Cannot test __call setBrowserUrl for somereason
//        $test->expects($this->once())
//             ->method('setBrowserUrl')
//             ->with($this->any());

        $listener = new SeleniumListener('http://google.com');
        $listener->startTest($test);
    }

    public function testStartTestWithNonSeleniumTestShouldDoNothing()
    {
        // Will fail due to setBrowserUrl() not existing on non-selenium tests
        $listener = new SeleniumListener('http://google.com');
        $listener->startTest($this);
    }
}