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

namespace Core\Engine\View;

use Core\Engine;

/**
 * ViewAbstract Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ViewAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * View
     *
     * @var View
     */
    private $view;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->view = new Engine\View();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->view);
        \Zend_Controller_Action_HelperBroker::removeHelper('templateManager');
    }

    public function testConstructOptions()
    {
        $config = array(
            'streamFlag'     => false,
            'streamProtocol' => 'test',
            'streamWrapper'  => 'test',
            'streamFilter'   => 'filter'
        );

        $view = new Engine\View($config);

        $this->assertFalse($view->getStreamFlag());
        $this->assertEquals('test', $view->getStreamProtocol());
        $this->assertEquals('test', $view->getStreamWrapper());
        $this->assertEquals(array('filter'), $view->getStreamFilters());
    }

    public function testConstructOptionsFromZend()
    {
        $view = new Engine\View(array('lfiProtectionOn' => true));
        $this->assertEquals(true, $view->isLfiProtectionOn());
    }

    public function testGetStreamFilters()
    {
        $view = new Engine\View();
        $view->addStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testAddStreamFilterWithString()
    {
        $view = new Engine\View();
        $view->addStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testAddStreamFilterWithArray()
    {
        $view = new Engine\View();
        $view->addStreamFilter(array('test', 'test2'));

        $this->assertContains('test', $view->getStreamFilters());
        $this->assertContains('test2', $view->getStreamFilters());
    }

    public function testAddStreamFilterIsFluent()
    {
        $view = new Engine\View();
        $this->assertTrue($view->addStreamFilter('test') instanceof Engine\View);
    }

    public function testSetStreamFilterClearsFilters()
    {
        $view = new Engine\View();
        $view->addStreamFilter('test');
        $view->setStreamFilter(array());
        $this->assertNotContains('test', $view->getStreamFilters());

        $view->addStreamFilter('test');
        $view->setStreamFilter(null);
        $this->assertNotContains('test', $view->getStreamFilters());
    }

    public function testSetStreamFilterAddsFromString()
    {
        $view = new Engine\View();
        $view->setStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testSetStreamFilterAddsFromArray()
    {
        $view = new Engine\View();
        $view->setStreamFilter(array('test', 'test2'));

        $this->assertContains('test', $view->getStreamFilters());
        $this->assertContains('test2', $view->getStreamFilters());
    }

    public function testSetStreamFlagIsFluent()
    {
        $view = new Engine\View();
        $this->assertEquals($view, $view->setStreamFlag(true));
    }

    public function testSetStreamFlagChangesFlag()
    {
        $view = new Engine\View();
        $view->setStreamFlag(false);

        $this->assertFalse($view->getStreamFlag());
    }

    public function testSetStreamProtocolThrowsExceptionOnEmptyProtocol()
    {
        $view  = new Engine\View();
        $test1 = null;
        $test2 = null;
        $test3 = null;

        try {
            $view->setStreamProtocol('');
        } catch (Exception $e) {
            $test1 = $e;
        }

        try {
            $view->setStreamProtocol(false);
        } catch (Exception $e) {
            $test2 = $e;
        }

        try {
            $view->setStreamProtocol('0');
        } catch (Exception $e) {
            $test3 = $e;
        }

        if (!$test1 instanceof Exception) {
            $this->fail();
        }

        if (!$test2 instanceof Exception) {
            $this->fail();
        }

        if (!$test3 instanceof Exception) {
            $this->fail();
        }
    }

    public function testSetStreamProtocolIsFluent()
    {
        $view = new Engine\View();
        $this->assertEquals($view, $view->setStreamProtocol('test'));
    }


    public function testSetStreamProtocolIsSet()
    {
        $view = new Engine\View();
        $view->setStreamProtocol('test');

        $this->assertEquals('test', $view->getStreamProtocol());
    }

    public function testGetStreamProtocol()
    {
        $view = new Engine\View();
        $this->assertEquals('view', $view->getStreamProtocol());
    }

    public function testSetStreamWrapperIsFluent()
    {
        $view = new Engine\View();
        $this->assertEquals($view, $view->setStreamWrapper('test'));
    }

    public function testSetStreamWrapperWorks()
    {
        $view = new Engine\View();
        $view->setStreamWrapper('test');

        $this->assertEquals('test', $view->getStreamWrapper());
    }

    public function testGetStreamWrapperReturnsString()
    {
        $view = new Engine\View();
        $this->assertEquals('Core\Engine\View\StreamWrapper\FilterWrapper', $view->getStreamWrapper());
    }

    public function testRenderDoesNothingWithoutStreamFlag()
    {
        $view = new Engine\View();
        $view->setStreamFlag(false)
             ->addStreamFilter('ShortTags')
             ->addScriptPath(dirname(__FILE__) . '/Scripts');

        if (ini_get('short_open_tag')) {
            $this->assertEquals('>', $view->render('run.phtml'));
        } else {
            $this->assertEquals('<?=@ \'>\'; ?>', $view->render('run.phtml'));
        }
    }

    public function testRenderUsesStream()
    {
        $view = new Engine\View();
        $view->addStreamFilter('ShortTags')
             ->addScriptPath(dirname(__FILE__) . '/Scripts');

        $this->assertEquals('&gt;', $view->render('run.phtml'));
    }

    public function testPluginLoaderAddsCoreFilters()
    {
        $view = new Engine\View();

        // Issue 43
        if (!method_exists($view, 'getPluginLoader')) {
            $this->markTestSkipped();
        }

        $pluginLoader = $view->getPluginLoader('filter');
        $this->assertContains(array('Zend/View/Filter/'), $pluginLoader->getPaths());
        $this->assertContains(array('Core/Engine/View/Filter/'), $pluginLoader->getPaths());
    }

    public function testRenderRegistersWrapperAndUnregistersIt()
    {
        $view = new Engine\View();
        @stream_wrapper_unregister($view->getStreamProtocol());
        $view->addStreamFilter('ShortTags')
             ->addScriptPath(dirname(__FILE__) . '/Scripts');


        $this->assertEquals('&gt;', $view->render('run.phtml'));
        $this->assertNotContains($view->getStreamProtocol(), stream_get_wrappers());
    }

    public function testRenderUnregistersExistingWrapperAndReregistersThem()
    {
        $view = new Engine\View();
        $view->addStreamFilter('ShortTags')
             ->addScriptPath(dirname(__FILE__) . '/Scripts');

        stream_wrapper_register($view->getStreamProtocol(), $view->getStreamWrapper());

        $this->assertEquals('&gt;', $view->render('run.phtml'));
        $this->assertContains($view->getStreamProtocol(), stream_get_wrappers());
    }

    public function testPluginLoaderAddsCoreHelpers()
    {
        $view = new Engine\View();

        // Issue 43
        if (!method_exists($view, 'getPluginLoader')) {
            $this->markTestSkipped();
        }

        $pluginLoader = $view->getPluginLoader('Helper');

        $this->assertContains(array('Zend/View/Helper/'), $pluginLoader->getPaths());
        $this->assertContains(array('Core/Engine/View/Helper/'), $pluginLoader->getPaths());
    }

    public function testCall()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        
        $view = new Engine\View();
        $view->baseUrl();

        $helper = new \Core\Engine\Template\Controller\Action\Helper\TemplateManager(
            new \Core\Engine\Template\TemplateManager()
        );
        \Zend_Controller_Action_HelperBroker::addHelper($helper);
        $view->templateUrl();
    }

    public function testLoadStreamWrapper()
    {
        $this->markTestIncomplete();
    }

    public function testRun()
    {
        $this->markTestIncomplete();
    }
}