<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

use Core\HalfPipe\Entity,
    Core\HalfPipe\Service;

/**
 * Index controller
 *
 * @author    Geoffrey Tran
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Sitemap_IndexController extends Zend_Controller_Action
{
    /**
     * Init
     */
    public function init()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', 'xml')
                      ->initContext();
    }

    /**
     * Home
     *
     * @return void
     */
    public function indexAction()
    {
        /* @var $serviceContainer \Core\Engine\Controller\Action\Helper\ServiceContainer */
        $serviceContainer = $this->getHelper('ServiceContainer')->getContainer();

        /* @var $navigationService Service\NavigationService */
        $navigationService = $serviceContainer->getService('service.navigation');

        $navigation = $navigationService->buildSitemap();
        
        $this->view->assign(array(
            'navigation' => $navigation
        ));


        $this->getResponse()
             ->setHeader('Expires', '', true)
             ->setHeader('Cache-Control', 'max-age=3800, public', true)
             ->setHeader('Pragma', '', true);
    }
}
