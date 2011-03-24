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
 * ServiceContainer DI Action Helper
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ServiceContainer extends HelperAbstract
{
    /**
     * Container
     *
     * @var DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Get container
     *
     * @return DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        if (!$this->container instanceof DependencyInjection\ContainerInterface) {
            $container = $this->getFrontController()
                              ->getParam('bootstrap')
                              ->getResource('di');
            $this->container = $container;
        }

        return $this->container;
    }

    /**
     * Set container
     *
     * @param ContainerInterface $container
     * @return ServiceContainer
     */
    public function setContainer(DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Proxy
     *
     * @param string $service
     */
    public function direct($service)
    {

        return $this->getContainer()->getService($service);
    }
}