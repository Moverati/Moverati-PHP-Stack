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

use Core\Engine\Template\Controller\Action\Helper;

/**
 * AssetUrl
 *
 * @author    Josh Team
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class AssetUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateUrl
     */
    protected $object;

    private $templateDir;
    private $templateName;
    private $templatePub;

    protected function setUp()
    {
        $view         = new \Zend_View();
        $this->object = new AssetUrl;

        $this->templateDir  = '.';
        $this->templateName = 'activeTest';
        $this->templatePub  = '/templates';

        $tm   = new \Core\Engine\Template\TemplateManager();
        $tm->addTemplate($this->templateDir, $this->templateName, $this->templatePub);
        $tm->setActiveTemplate($this->templateName);

        $this->object->setView($view);
        $this->object->setTemplateActionHelper(new \Core\Engine\Template\Controller\Action\Helper\TemplateManager($tm));
    }
    
    public function tearDown()
    {
        \Zend_Controller_Action_HelperBroker::removeHelper('templateManager');
    }


    public function testCallingAsFunction()
    {
        $class = $this->object;
        $this->assertEquals($this->templatePub . '/assets', $class());
    }

    public function testCallingAsFunctionWithPath()
    {
        $class = $this->object;
        $path  = '/foo';
        $this->assertEquals($this->templatePub . '/assets' . $path, $class($path));
    }
}