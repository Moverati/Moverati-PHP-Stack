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

namespace Core\Engine\Template;

/**
 * Template Manager Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TemplateManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        TemplateManager::resetMvcInstance();
    }

    public function testStartMvc()
    {
        $result = TemplateManager::startMvc();
        $this->assertSame('Core\Engine\Template\TemplateManager', get_class($result));
    }

    public function testGetMvcInstance()
    {
        $result = TemplateManager::getMvcInstance();
        $this->assertNull($result);
    }

    public function testSetOptionsWithSet()
    {
        $this->object->setOptions(array('helperClass' => 'ZZZ'));
        $this->assertSame('ZZZ', $this->object->getHelperClass());
    }

    public function testSetOptionsWithAdd()
    {
        $template = '.';
        $this->object->setOptions(array('template' => $template));
        $rTemplate = $this->object->getTemplate($template);
        $this->assertSame($template, $rTemplate['path']);
    }

    public function testGetMvcEnabled()
    {
        $tm = new TemplateManager;
        $this->assertFalse($tm->getMvcEnabled());
        TemplateManager::startMvc();
        $this->assertTrue(TemplateManager::getMvcInstance()->getMvcEnabled());
    }

    public function testSetAndGetMvcSuccessfulActionOnly()
    {
        $this->object->setMvcSuccessfulActionOnly(true);
        $this->assertTrue($this->object->getMvcSuccessfulActionOnly());
    }

    public function testSetActiveTemplateWithInvalidTemplate()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $this->object->setActiveTemplate('DoesNotExists');
    }

    public function testSetAndGetActiveTemplateWithValidTemplate()
    {
        $templateName = 'valid';
        $this->object->addTemplate('.', $templateName);
        $this->object->setActiveTemplate($templateName);
        $this->assertSame($this->object->getActiveTemplate(), $templateName);
    }

    public function testGetActiveTemplatePathNoActiveTemplate()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $this->object->getActiveTemplatePath();
    }

    public function testGetActiveTemplatePath()
    {
        $path = '.';
        $name = 'valid';
        $publicPath = '/public';
        $this->object->addTemplate($path, $name, $publicPath);
        $this->object->setActiveTemplate($name);
        $this->assertSame($path, $this->object->getActiveTemplatePath());
    }

    public function testGetActiveTemplatePublicPath()
    {
        $path = '.';
        $name = 'valid';
        $publicPath = '/public';
        $this->object->addTemplate($path, $name, $publicPath);
        $this->object->setActiveTemplate($name);
        $this->assertSame($publicPath, $this->object->getActiveTemplatePublicPath());
    }

    public function testAddTemplatesPathWhenExist()
    {
        $this->object->addTemplatesPath(PATH_PROJECT . '/application/templates');
    }

    public function testAddTemplatesPathWhenArrayOfStringsAndExist()
    {
        $paths = array(PATH_PROJECT . '/application/templates', PATH_PROJECT . '/application');
        $this->object->addTemplatesPath($paths);
    }

    public function testAddTemplatesPathWhenArrayOfArraysAndExist()
    {
        $paths = array(array('path' => PATH_PROJECT . '/application/templates'), array('path'=>PATH_PROJECT . '/application', 'publicPath'=>'pub'));
        $this->object->addTemplatesPath($paths);
    }

    public function testAddTemplatesPathWhenNotReadible()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $this->object->addTemplatesPath('/does/not/exist');
    }

    public function testAddTemplatesPathWhenInvalidPathIsProvided()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $this->object->addTemplatesPath(array($this));
    }

    public function testAddTemplateInvalidDirectory()
    {
        $this->setExpectedException('Core\Engine\Template\Exception');
        $dir  = '/invalid/directory';
        $this->object->addTemplate($dir);
    }


    public function testAddTemplateValidTemplateDirectory()
    {
        $name = 'test';
        $dir  = '.';
        $this->object->addTemplate($dir, $name);
        $this->assertTrue($this->object->hasTemplate($name));
    }

    public function testSetAndGetTemplates()
    {
        $templates = array('foo', 'bar');
        $this->object->setTemplates($templates);
        $this->assertEquals($templates, $this->object->getTemplates());
    }

    public function testSetAndGetPluginClass()
    {
        $pluginClass = 'Foo';
        $this->object->setPluginClass($pluginClass);
        $this->assertEquals($pluginClass, $this->object->getPluginClass());
    }
}