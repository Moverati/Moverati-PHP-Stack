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

namespace Core\Engine\Entity;

use Core\Engine;

/**
 * MockEntity Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class MockEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * MockEntity
     * 
     * @var MockEntity
     */
    private $entity;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->entity = new MockEntity();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->entity);
    }

    public function testEntityImplementsIndexableInterface()
    {
        $this->assertType('Core\Engine\Search\Lucene\IndexableInterface', $this->entity);
    }

    public function testGetId()
    {
        $this->assertNull($this->entity->getId());
    }

    public function testGetSearchDocumentId()
    {
        $this->assertEquals(md5(get_class($this->entity) . $this->entity->getId()), $this->entity->getSearchDocumentId());
    }

    public function testGetSearchDocument()
    {
        $this->assertType('Zend_Search_Lucene_Document', $this->entity->getSearchDocument());

        $this->assertContains('id', $this->entity->getSearchDocument()->getFieldNames());
    }

    public function testIsIndexable()
    {
        $this->assertTrue($this->entity->isSearchIndexable());
    }
}