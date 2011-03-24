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
 * SeleniumTestCase test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SeleniumTestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAndSetBrowserUrl()
    {
        /* @var $testCase SeleniumTestCase */
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');
        $testCase->setBrowserUrl('http://test/');
        $this->assertEquals('http://test/', $testCase->getBrowserUrl());

        $r = new \ReflectionObject($testCase);
        /* @var $prop \ReflectionProperty */
        $prop = $r->getProperty('drivers');
        $prop->setAccessible(true);

        $drivers = $prop->getValue($testCase);

        $r = new \ReflectionObject($drivers[0]);
        /* @var $prop \ReflectionProperty */
        $prop = $r->getProperty('browserUrl');
        $prop->setAccessible(true);

        $browserUrl = $prop->getValue($drivers[0]);

        $this->assertEquals('http://test/', $browserUrl);
    }

    public function testClassExtendsSeleniumTestCase()
    {
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');
        $this->assertType('Core\Engine\PHPUnit\Selenium\SeleniumTestCase', $testCase);
    }

    public function testClassIsAbstract()
    {
        $r = new \ReflectionClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');
        $this->assertTrue($r->isAbstract());
    }

    public function testRunAddsSeleniumListener()
    {
        \PHPUnit_Util_Configuration::getInstance(__DIR__ . '/SeleniumTestCase/single-listener.xml');

        /* @var $testCase SeleniumTestCase */
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');

        $result = $this->getMock('PHPUnit_Framework_TestResult', array('addListener'));

        $result->expects($this->once())
               ->method('addListener')
               ->with($this->isInstanceOf('Core\Engine\PHPUnit\Selenium\SeleniumListener'));


        $testCase->run($result);
    }

    /**
     * @depends testRunAddsSeleniumListener
     */
    public function testRunAddsMultipleSeleniumListeners()
    {
        \PHPUnit_Util_Configuration::getInstance(__DIR__ . '/SeleniumTestCase/multiple-listeners.xml');

        /* @var $testCase SeleniumTestCase */
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');

        $result = $this->getMock('PHPUnit_Framework_TestResult', array('addListener'));

        $result->expects($this->once())
               ->method('addListener')
               ->with($this->isInstanceOf('Core\Engine\PHPUnit\Selenium\SeleniumListener'));

        $testCase->run($result);
    }

    public function testRunWithoutArgumentsCreatesResult()
    {
        /* @var $testCase SeleniumTestCase */
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');
        $result   = $testCase->run();

        $this->assertType('PHPUnit_Framework_TestResult', $result);
    }

    /**
     * @depends testRunAddsSeleniumListener
     */
    public function testRunWithExistingListenerIgnoresAddingExtraSeleniumOnes()
    {
        /* @var $testCase SeleniumTestCase */
        $testCase = $this->getMockForAbstractClass('Core\Engine\PHPUnit\Selenium\SeleniumTestCase');

        $result = new \PHPUnit_Framework_TestResult();

        $result->addListener(new SeleniumListener('test'));

        $testCase->run($result);

        // Code used to prevent setting multiple listeners
        $r = new \ReflectionObject($result);
        /* @var $prop \ReflectionProperty */
        $prop = $r->getProperty('listeners');
        $prop->setAccessible(true);

        $listeners = $prop->getValue($result);
        $this->assertEquals(1, count($listeners));
    }
}