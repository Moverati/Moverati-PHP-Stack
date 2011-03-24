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
 * Selenium test listener
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SeleniumListener implements \PHPUnit_Framework_TestListener
{
    /**
     * The base browser url
     * 
     * @var string
     */
    private $browserUrl;

    /**
     * Construct
     *
     * @param string $browserUrl
     */
    public function __construct($browserUrl = null)
    {
        $this->setBrowserUrl($browserUrl);
    }

    /**
     * Get the base browser url
     *
     * @return string
     */
    public function getBrowserUrl()
    {
        return $this->browserUrl;
    }

    /**
     * Set the base browser url
     *
     * @param string $url
     * @return SeleniumListener
     */
    public function setBrowserUrl($url)
    {
        $this->browserUrl = rtrim($url, '/\\') . '/';
        return $this;
    }

    /**
     * An error occurred.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     * @codeCoverageIgnoreStart
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * A failure occurred.
     *
     * @param  \PHPUnit_Framework_Test                 $test
     * @param  \PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    /**
     * Incomplete test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }


    /**
     * Skipped test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * A test suite started.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    /**
     * A test suite ended.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    /**
     * A test started.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @codeCoverageIgnoreEnd
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        if (!$this->isSeleniumTest($test)) {
            return;
        }

        $browserUrl = $this->getBrowserUrl();
        $test->setBrowserUrl($browserUrl);
    }

    /**
     * A test ended.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
    }

    /**
     * Is test a selenium one
     *
     * @return boolean
     */
    protected function isSeleniumTest(\PHPUnit_Framework_Test $test)
    {
        return ($test instanceof \PHPUnit_Extensions_SeleniumTestCase);
    }
}