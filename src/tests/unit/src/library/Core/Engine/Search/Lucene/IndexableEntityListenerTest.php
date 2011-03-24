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

namespace Core\Engine\Search\Lucene;

use Doctrine\ORM;

/**
 * IndexableEntityListener
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class IndexableEntityListenerTest extends \PHPUnit_Framework_TestCase
{
    private $defaultIndexClass;
    private $defaultIndexPath;

    protected function setUp()
    {
        $this->defaultIndexClass = IndexManager::getDefaultIndexClass();
        $this->defaultIndexPath  = IndexManager::getDefaultIndexPath();
        $this->deleteAll(PATH_PROJECT . '/tests/unit/data/indexes/IndexableListenerTest');
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexableListenerTest');
    }

    protected function tearDown()
    {
        IndexManager::setDefaultIndexClass($this->defaultIndexClass);
        IndexManager::getDefaultIndexPath($this->defaultIndexPath);
        $this->deleteAll(PATH_PROJECT . '/tests/unit/data/indexes/IndexableListenerTest');
    }

    public function testConstructorAcceptsIndexManager()
    {
        $indexManager = IndexManager::factory();
        $listener     = new IndexableEntityListener($indexManager);
        $this->assertSame($indexManager, $listener->getIndexManager());
    }

    public function testGetAndSetIndexManager()
    {
        $indexManager = IndexManager::factory();
        $listener     = new IndexableEntityListener($indexManager);
        $this->assertSame($indexManager, $listener->getIndexManager());

        $indexManager = IndexManager::factory();
        $listener->setIndexManager($indexManager);
        $this->assertSame($indexManager, $listener->getIndexManager());
    }

    public function testGetSubscribedEvents()
    {
        $indexManager = IndexManager::factory();
        $listener     = new IndexableEntityListener($indexManager);
        $this->assertContains(ORM\Events::postPersist, $listener->getSubscribedEvents());
        $this->assertContains(ORM\Events::postRemove, $listener->getSubscribedEvents());
        $this->assertContains(ORM\Events::postUpdate, $listener->getSubscribedEvents());
    }

    public function testPostPersist()
    {
        $indexManager = IndexManager::factory();
        $docCount     = $indexManager->getSearchIndex()->count();

        $listener     = new IndexableEntityListener($indexManager);

        $entity = $this->getMock('Core\Engine\Search\Lucene\IndexableInterface', array('getSearchDocumentId', 'getSearchDocument', 'isSearchIndexable'));

        $entity->expects($this->any())
               ->method('getSearchDocumentId')
               ->withAnyParameters()
               ->will($this->returnValue('postPersist'));

        $entity->expects($this->any())
               ->method('getSearchDocument')
               ->withAnyParameters()
               ->will($this->returnValue(new \Zend_Search_Lucene_Document()));

        $entity->expects($this->any())
               ->method('isSearchIndexable')
               ->withAnyParameters()
               ->will($this->returnValue(true));
        
        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($entity, $em);
        $listener->postPersist($eventArgs);

        $this->assertEquals($docCount + 1, $indexManager->getSearchIndex()->count());
    }

    public function testPostPersistReturnsOnNonIndexableEntity()
    {
        $indexManager = IndexManager::factory();
        $docCount     = $indexManager->getSearchIndex()->count();

        $listener     = new IndexableEntityListener($indexManager);

        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($this, $em);
        $listener->postPersist($eventArgs);
        $this->assertEquals($docCount, $indexManager->getSearchIndex()->count());
    }

    public function testPostUpdate()
    {
        $indexManager = IndexManager::factory();

        $listener     = new IndexableEntityListener($indexManager);

        $entity = $this->getMock('Core\Engine\Search\Lucene\IndexableInterface', array('getSearchDocumentId', 'getSearchDocument', 'isSearchIndexable'));

        $entity->expects($this->any())
               ->method('getSearchDocumentId')
               ->withAnyParameters()
               ->will($this->returnValue('postUpdate'));

        $doc = new \Zend_Search_Lucene_Document();
        $doc->addField(\Zend_Search_Lucene_Field::keyword($indexManager->getIdKey(), 'postUpdate'));

        $entity->expects($this->any())
               ->method('getSearchDocument')
               ->withAnyParameters()
               ->will($this->returnValue($doc));

        $entity->expects($this->any())
               ->method('isSearchIndexable')
               ->withAnyParameters()
               ->will($this->returnValue(true));

        $indexManager->index($entity);
        $docCount     = $indexManager->getSearchIndex()->count();


        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($entity, $em);
        $listener->postUpdate($eventArgs);

        $this->assertEquals($docCount, $indexManager->getSearchIndex()->numDocs());
    }

    public function testPostUpdateReturnsOnNonIndexableEntity()
    {
        $indexManager = IndexManager::factory();
        $docCount     = $indexManager->getSearchIndex()->count();

        $listener     = new IndexableEntityListener($indexManager);

        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($this, $em);
        $listener->postUpdate($eventArgs);
        $this->assertEquals($docCount, $indexManager->getSearchIndex()->numDocs());
    }

    public function testPostRemove()
    {
        $indexManager = IndexManager::factory();
        $listener     = new IndexableEntityListener($indexManager);

        $entity = $this->getMock('Core\Engine\Search\Lucene\IndexableInterface', array('getSearchDocumentId', 'getSearchDocument', 'isSearchIndexable'));
        
        $entity->expects($this->any())
               ->method('getSearchDocumentId')
               ->withAnyParameters()
               ->will($this->returnValue('postRemove'));
        
        $doc = new \Zend_Search_Lucene_Document();
        $doc->addField(\Zend_Search_Lucene_Field::keyword($indexManager->getIdKey(), 'postRemove'));

        $entity->expects($this->any())
               ->method('getSearchDocument')
               ->withAnyParameters()
               ->will($this->returnValue($doc));

        $entity->expects($this->any())
               ->method('isSearchIndexable')
               ->withAnyParameters()
               ->will($this->returnValue(true));

        $indexManager->index($entity);

        $this->assertEquals(1, $indexManager->getSearchIndex()->count());

        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($entity, $em);
        $listener->postRemove($eventArgs);

        $indexManager->delete('postRemove');
        
        $this->assertEquals(0, $indexManager->getSearchIndex()->numDocs());
    }

    public function testPostRemoveReturnsOnNonIndexableEntity()
    {
        $indexManager = IndexManager::factory();
        $docCount     = $indexManager->getSearchIndex()->count();

        $listener     = new IndexableEntityListener($indexManager);

        $em = \Core\Tests\EntityHelper::getEntityManager();
        $eventArgs = new ORM\Event\LifecycleEventArgs($this, $em);
        $listener->postRemove($eventArgs);
        $this->assertEquals($docCount, $indexManager->getSearchIndex()->numDocs());
    }

    private function deleteAll($directory, $empty = false)
    {
        if(substr($directory,-1) == '/') {
            $directory = substr($directory, 0, -1);
        }

        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);

            while ($contents = readdir($directoryHandle)) {
                if($contents != '.' && $contents != '..') {
                    $path = $directory . '/' . $contents;

                    if(is_dir($path)) {
                        $this->deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }
}