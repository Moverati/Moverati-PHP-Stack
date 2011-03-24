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

namespace Core\Engine\Cache\Backend;

/**
 * Error Controller
 *
 * @author    Daniel Cousineau
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Memcache extends \Zend_Cache_Backend_Memcached
{
    /**
     * Constructor
     *
     * @param array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        $options['servers'] = array();
        parent::__construct($options);

        if( isset($options['memcache']) && $options['memcache'] instanceOf \Memcache ) {
            $this->_memcache = $options['memcache'];
        }
    }
}