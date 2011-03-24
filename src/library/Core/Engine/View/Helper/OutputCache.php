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

use Core\Engine,
    Core\Engine\Template;

/**
 * OutputCache
 *
 * @author    Daniel Cousineau
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class OutputCache extends HelperAbstract
{
    /**
     *
     * @var \Zend_Cache
     */
    protected $cache;

    /**
     *
     * @return \Zend_Cache_Frontend_Output
     */
    public function __invoke()
    {
        if( !isset($this->cache) ) {
            /* @var $cache \Zend_Cache_Frontend_Output */
            $cache = $this->getContainer()->getService('service.cache.zend.output');

            $this->cache = $cache;
        }

        return $cache;
    }
}