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

/**
 * Helper Abstract
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
abstract class HelperAbstract extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);
        if (strpos($className, '\\') !== false) {
            $helperName = strrchr($className, '\\');
            return ltrim($helperName, '\\');
        } else if (strpos($className, '_') !== false) {
            $helperName = strrchr($className, '_');
            return ltrim($helperName, '_');
        } else {
            return $className;
        }
    }

}