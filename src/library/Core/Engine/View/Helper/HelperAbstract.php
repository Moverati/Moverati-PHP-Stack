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
    Symfony\Components\DependencyInjection;

/**
 * View Helper Abstract
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
abstract class HelperAbstract extends \Zend_View_Helper_Abstract
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

            $container = \Zend_Controller_Front::getInstance()
                                               ->getParam('bootstrap')
                                               ->getResource('di');
            $this->container = $container;
        }

        return $this->container;
    }

    /**
     * Get view object
     *
     * @return \Zend_View_Abstract
     */
    public function getView()
    {
        if (!$this->view instanceof \Zend_View_Abstract) {
            throw new Exception(
                'A view object of instance Zend_View_Abstract is not set to this helper.'
            );
        }

        return $this->view;
    }
}