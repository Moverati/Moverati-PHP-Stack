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
 * FlashMessenger
 *
 * Uses the flash messenger action helper
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FlashMessenger extends HelperAbstract
{
    /**
     * Retrieve the FlashMessenger action helper instance with the ability
     * to set the namespace for simplicity
     *
     * @param string $namespace
     * @return \Zend_Controller_Action_Helper_FlashMessenger
     */
    public function __invoke($namespace = null)
    {
        // Retrieve instance
        $flashMessenger = \Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        // Set namespace to retrieve
        if ($namespace !== null) {
            $flashMessenger->setNamespace($namespace);
        }

        return $flashMessenger;
    }
}