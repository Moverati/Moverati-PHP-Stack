<?php

namespace Symfony\Framework\WebBundle\Listener;

use Symfony\Foundation\LoggerInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\Routing\RouterInterface;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * RequestParser.
 *
 * @package    Symfony
 * @subpackage Framework_WebBundle
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class RequestParser
{
    protected $container;
    protected $router;
    protected $logger;

    public function __construct(ContainerInterface $container, RouterInterface $router, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function register()
    {
        $this->container->getEventDispatcherService()->connect('core.request', array($this, 'resolve'));
    }

    public function resolve(Event $event)
    {
        $request = $event->getParameter('request');

        if (!$event->getParameter('main_request'))
        {
            return;
        }

        // set the context even if the parsing does not need to be done
        // to have correct link generation
        $this->router->setContext(array(
            'base_url'  => $request->getBaseUrl(),
            'method'    => $request->getMethod(),
            'host'      => $request->getHost(),
            'is_secure' => $request->isSecure(),
        ));
        $this->container->setParameter('request.base_path', $request->getBasePath());

        if ($request->path->has('_bundle'))
        {
            return;
        }

        if (false !== $parameters = $this->router->match($request->getPathInfo()))
        {
            if (null !== $this->logger)
            {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], str_replace("\n", '', var_export($parameters, true))));
            }

            $request->path->replace($parameters);
        }
        elseif (null !== $this->logger)
        {
            $this->logger->err(sprintf('No route found for %s', $request->getPathInfo()));
        }
    }
}
