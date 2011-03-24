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

use Core\Engine;

/**
 * TimeSince Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TimeSinceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var TimeSince
     */
    private $helper;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $view   = new Engine\View();
        $helper = new TimeSince();
        $helper->setView($view);

        $this->helper = $helper;
    }

    /**
     * Cleans up the environment after running a test.
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->helper);
    }

    public function testTimeSinceUsesCurrentTime()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day', $helper->__invoke(strtotime('-1 day', time())));
        $this->assertEquals('2 weeks', $helper->__invoke(strtotime('-2 weeks', time())));
    }

    public function testTimeSinceUsesCustomTime()
    {
        $helper = $this->helper;

        $this->assertEquals('2 days', $helper->__invoke(strtotime('-3 day', time()), strtotime('-1 day', time())));
        $this->assertEquals('2 years', $helper->__invoke(strtotime('-3 year', time()), strtotime('-1 year', time())));
    }

    public function testTimeSinceReturnsYear()
    {
        $helper = $this->helper;

        $this->assertEquals('1 year', $helper->__invoke(strtotime('-1 year', time())));
        $this->assertEquals('2 years', $helper->__invoke(strtotime('-3 years', time()), strtotime('-1 year', time())));
        $this->assertEquals('-2 years', $helper->__invoke(strtotime('+2 year', time())));
    }

    public function testTimeSinceReturnsMonth()
    {
        $this->markTestIncomplete("Wigging out");
        /*
        $helper = $this->helper;

        $this->assertEquals('1 month', $helper->__invoke(strtotime('-1 month', time())));
        $this->assertEquals('2 months', $helper->__invoke(strtotime('-3 months', time()), strtotime('-1 month', time())));
        $this->assertEquals('-2 months', $helper->__invoke(strtotime('+2 months', time())));
        */
    }

    public function testTimeSinceReturnsAWeek()
    {
        $helper = $this->helper;

        $this->assertEquals('1 week', $helper->__invoke(strtotime('-1 week', time())));
        $this->assertEquals('2 weeks', $helper->__invoke(strtotime('-2 weeks', time())));
        $this->assertEquals('-2 weeks', $helper->__invoke(strtotime('+2 weeks', time())));
    }

    public function testTimeSinceReturnsADay()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day', $helper->__invoke(strtotime('-1 day', time())));
        $this->assertEquals('2 days', $helper->__invoke(strtotime('-2 days', time())));
        $this->assertEquals('-2 days', $helper->__invoke(strtotime('+2 days', time())));
    }

    public function testTimeSinceReturnsAnHour()
    {
        $helper = $this->helper;

        $this->assertEquals('1 hour', $helper->__invoke(strtotime('-1 hour', time())));
        $this->assertEquals('2 hours', $helper->__invoke(strtotime('-2 hours', time())));
        $this->assertEquals('-2 hours', $helper->__invoke(strtotime('+2 hours', time())));
    }

    public function testTimeSinceReturnsAMinute()
    {
        $helper = $this->helper;

        $this->assertEquals('1 minute', $helper->__invoke(strtotime('-1 minute', time())));
        $this->assertEquals('2 minutes', $helper->__invoke(strtotime('-2 minutes', time())));
        $this->assertEquals('-2 minutes', $helper->__invoke(strtotime('+2 minutes', time())));
    }

    public function testTimeSinceReturnsASecond()
    {
        $helper = $this->helper;

        $this->assertEquals('1 second', $helper->__invoke(strtotime('-1 second', time())));
        $this->assertEquals('2 seconds', $helper->__invoke(strtotime('-2 seconds', time())));
        $this->assertEquals('-2 seconds', $helper->__invoke(strtotime('+2 seconds', time())));
    }

    public function testTimeSinceReturnsSmallerChunk()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day and 5 hours', $helper->__invoke(strtotime('-1 day, -5 hours', time())));
        $this->assertEquals('2 days and 5 hours', $helper->__invoke(strtotime('-2 days, -5 hours', time())));
        $this->assertEquals('2 days', $helper->__invoke(strtotime('-2 days, -5 minutes', time())));
        $this->assertEquals('-2 days and -5 hours', $helper->__invoke(strtotime('+2 days, +5 hours', time())));
        $this->assertEquals('1 week and 2 days', $helper->__invoke(strtotime('-1 week, -2 days', time())));
    }

    public function testTimeSinceReturnsLessThanASecond()
    {
        $helper = $this->helper;
        $this->assertEquals('less than a second', $helper->__invoke(time()));
    }

    public function testTimeSinceWorksWithTranslate()
    {
        $data = array(
            'less than a second' => 'bar',
            '%d weeks'           => '%d bar',
            '%d days and %d hours' => '%d bar and %d foo'
        );

        $translate = new \Zend_Translate('array', $data, 'en');
        $helper    = $this->helper;
        \Zend_Registry::set('Zend_Translate', $translate);

        $this->assertEquals('bar', $helper->__invoke(time()));
        $this->assertEquals('2 bar', $helper->__invoke(strtotime('-2 weeks', time())));
        $this->assertEquals('2 bar and 2 foo', $helper->__invoke(strtotime('-2 days, -2 hours', time())));

        $registry = \Zend_Registry::getInstance();
        unset($registry['Zend_Translate']);
    }
}


if (!\function_exists('\Core\Engine\View\Helper\time')) {
    /**
     * Time function to ensure a clean environment
     *
     * @staticvar integer $time
     * @return integer
     */
    function time()
    {
        static $time;

        if ($time === null) {
            $time = \time();
        }
        return $time;
    }
}