<?php

namespace Symfony\Framework\WebBundle;

use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\BuilderConfiguration;
use Symfony\Framework\WebBundle\DependencyInjection\WebExtension;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Bundle.
 *
 * @package    Symfony
 * @subpackage Framework_WebBundle
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Bundle extends BaseBundle
{
    public function buildContainer(ContainerInterface $container)
    {
        Loader::registerExtension(new WebExtension());

        $dirs = array('%kernel.root_dir%/views/%%bundle%%/%%controller%%/%%name%%%%format%%.php');
        foreach ($container->getParameter('kernel.bundle_dirs') as $dir)
        {
            $dirs[] = $dir.'/%%bundle%%/Resources/views/%%controller%%/%%name%%%%format%%.php';
        }
        $container->setParameter('templating.loader.filesystem.path', $dirs);

        $configuration = new BuilderConfiguration();
        if ($container->getParameter('kernel.debug'))
        {
            $loader = new XmlFileLoader(__DIR__.'/Resources/config');
            $configuration->merge($loader->load('debug.xml'));
        }

        return $configuration;
    }
}
