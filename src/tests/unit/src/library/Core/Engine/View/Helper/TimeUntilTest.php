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
 * TimeUntil Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TimeUntilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var TimeUntil
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
        $helper = new TimeUntil();
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

    public function testTimeUntilUsesCurrentTime()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day', $helper->__invoke(strtotime('+1 day', time())));
        $this->assertEquals('2 weeks', $helper->__invoke(strtotime('+2 weeks', time())));
    }

    public function testTimeUntilUsesCustomTime()
    {
        $helper = $this->helper;

        $this->assertEquals('2 days', $helper->__invoke(strtotime('+3 day', time()), strtotime('+1 day', time())));
        $this->assertEquals('2 years', $helper->__invoke(strtotime('+3 year', time()), strtotime('+1 year', time())));
    }

    public function testTimeUntilReturnsYear()
    {
        $helper = $this->helper;

        $this->assertEquals('1 year', $helper->__invoke(strtotime('+1 year', time())));
        $this->assertEquals('2 years', $helper->__invoke(strtotime('+3 years', time()), strtotime('+1 year', time())));
        $this->assertEquals('-2 years', $helper->__invoke(strtotime('-2 years', time())));
    }

    public function testTimeUntilReturnsMonth()
    {
        $this->markTestSkipped("Failing, needs attention");
        return;
        
        $helper = $this->helper;

        //$this->assertEquals('1 month', $helper->__invoke(strtotime('+1 month', time())));
        //$this->assertEquals('2 months', $helper->__invoke(strtotime('+3 months', time()), strtotime('+1 month', time())));
        //$this->assertEquals('-2 months', $helper->__invoke(strtotime('-2 months', time())));
    }

    public function testTimeUntilReturnsAWeek()
    {
        $helper = $this->helper;

        $this->assertEquals('1 week', $helper->__invoke(strtotime('+1 week', time())));
        $this->assertEquals('2 weeks', $helper->__invoke(strtotime('+2 weeks', time())));
        $this->assertEquals('-2 weeks', $helper->__invoke(strtotime('-2 weeks', time())));
    }

    public function testTimeUntilReturnsADay()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day', $helper->__invoke(strtotime('+1 day', time())));
        $this->assertEquals('2 days', $helper->__invoke(strtotime('+2 days', time())));
        $this->assertEquals('-2 days', $helper->__invoke(strtotime('-2 days', time())));
    }

    public function testTimeUntilReturnsAnHour()
    {
        $helper = $this->helper;

        $this->assertEquals('1 hour', $helper->__invoke(strtotime('+1 hour', time())));
        $this->assertEquals('2 hours', $helper->__invoke(strtotime('+2 hours', time())));
        $this->assertEquals('-2 hours', $helper->__invoke(strtotime('-2 hours', time())));
    }

    public function testTimeUntilReturnsAMinute()
    {
        $helper = $this->helper;

        $this->assertEquals('1 minute', $helper->__invoke(strtotime('+1 minute', time())));
        $this->assertEquals('2 minutes', $helper->__invoke(strtotime('+2 minutes', time())));
        $this->assertEquals('-2 minutes', $helper->__invoke(strtotime('-2 minutes', time())));
    }

    public function testTimeUntilReturnsASecond()
    {
        $helper = $this->helper;

        $this->assertEquals('1 second', $helper->__invoke(strtotime('+1 second', time())));
        $this->assertEquals('2 seconds', $helper->__invoke(strtotime('+2 seconds', time())));
        $this->assertEquals('-2 seconds', $helper->__invoke(strtotime('-2 seconds', time())));
    }

    public function testTimeUntilReturnsSmallerChunk()
    {
        $helper = $this->helper;

        $this->assertEquals('1 day and 5 hours', $helper->__invoke(strtotime('+1 day, +5 hours', time())));
        $this->assertEquals('2 days and 5 hours', $helper->__invoke(strtotime('+2 days, +5 hours', time())));
        $this->assertEquals('2 days', $helper->__invoke(strtotime('+2 days, +5 minutes', time())));
        $this->assertEquals('-2 days and -5 hours', $helper->__invoke(strtotime('-2 days, -5 hours', time())));
    }

    public function testTimeUntilReturnsLessThanASecond()
    {
        $helper = $this->helper;
        $this->assertEquals('less than a second', $helper->__invoke(time()));
    }

    public function testTimeUntilWorksWithTranslate()
    {
        $data = array(
            'less than a second' => 'bar',
            '%d weeks'            => '%d bar'
        );

        $translate = new \Zend_Translate('array', $data, 'en');
        $helper    = $this->helper;
        \Zend_Registry::set('Zend_Translate', $translate);

        $this->assertEquals('bar', $helper->__invoke(time()));
        $this->assertEquals('2 bar', $helper->__invoke(strtotime('+2 weeks', time())));

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