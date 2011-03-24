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

use Symfony\Components\DependencyInjection;

/**
 * Service container test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceContainer
     */
    protected $helper;

    /**
     * Setup
     *
     */
    protected function setUp()
    {
        $this->helper = new ServiceContainer();
    }

    protected function tearDown()
    {
        \Zend_Controller_Front::getInstance()
                      ->resetInstance();
    }

    public function testGetContainerWithoutContainerWillGrabItFromZendApp()
    {
        \Zend_Controller_Front::getInstance()
                              ->resetInstance();

        $container = new \Zend_Registry(array('di' => new DependencyInjection\Container()));

        $application  = new \Zend_Application('dev');
        $bootstrap    = new \Zend_Application_Bootstrap_Bootstrap($application);
        $bootstrap->setContainer($container);
        
        \Zend_Controller_Front::getInstance()->setParam('bootstrap', $bootstrap);
        $helper = new ServiceContainer();

        $this->assertTrue($helper->getContainer() === $bootstrap->getResource('di'));
    }

    public function testGetAndSetContainer()
    {
        $newContainer = new DependencyInjection\Container();
        $container = clone $this->helper;
        $container->setContainer($newContainer);

        $this->assertTrue($container->getContainer() === $newContainer);

    }

    public function testSetContainerReturnsServiceContainer()
    {
        $newContainer = new DependencyInjection\Container();
        $container = clone $this->helper;
        $return    = $container->setContainer($newContainer);
        $this->assertTrue($return === $container);
    }

    public function testDirectReturnsService()
    {
        $newContainer = new DependencyInjection\Container();
        $newContainer->setService('test', $newContainer);

        $container = clone $this->helper;
        $container->setContainer($newContainer);
        
        $return    = $container->direct('test');

        $this->assertTrue($return === $newContainer);
    }
}