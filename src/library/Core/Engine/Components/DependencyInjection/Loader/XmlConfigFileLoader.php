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

namespace Core\Engine\Components\DependencyInjection\Loader;

use Symfony\Components\DependencyInjection,
    Symfony\Components\DependencyInjection\Loader;

/**
 * Loads an xml configuration file
 *
 * The difference is that we ignore if we cannot load a file. Useful
 * for xml configuration files.
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class XmlConfigFileLoader extends Loader\XmlFileLoader
{
    /**
     * Loads an array of XML files.
     *
     * @param  string $file An XML file path
     *
     * @return BuilderConfiguration A BuilderConfiguration instance
     */
    public function load($file)
    {
        try {
            $configuration = parent::load($file);
        } catch (\InvalidArgumentException $e) {
            $configuration = new DependencyInjection\BuilderConfiguration();
        }

        return $configuration;
    }
}