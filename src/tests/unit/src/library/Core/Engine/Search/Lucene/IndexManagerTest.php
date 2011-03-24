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

/**
 * IndexManagerTest
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class IndexManagerTest extends    \PHPUnit_Framework_TestCase
                       implements IndexableInterface
{
    private $defaultIndexClass;
    private $defaultIndexPath;
    private $indexable = true;

    protected function setUp()
    {
        $this->defaultIndexClass = IndexManager::getDefaultIndexClass();
        $this->defaultIndexPath  = IndexManager::getDefaultIndexPath();
        $this->deleteAll(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
    }

    protected function tearDown()
    {
        IndexManager::setDefaultIndexClass($this->defaultIndexClass);
        IndexManager::getDefaultIndexPath($this->defaultIndexPath);
        $this->deleteAll(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
    }

    public function testGetAndSetDefaultIndexClass()
    {
        IndexManager::setDefaultIndexClass('test');
        $this->assertEquals('test', IndexManager::getDefaultIndexClass());
    }

    public function testGetAndSetDefaultIndexPath()
    {
        IndexManager::setDefaultIndexPath('test');
        $this->assertEquals('test', IndexManager::getDefaultIndexPath());
    }

    public function testHasDefaultCacheThrowsException()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        if (!IndexManager::hasDefaultCache()) {
            IndexManager::getDefaultCache();
        }
    }

    public function testHasDefaultCache()
    {
        IndexManager::setDefaultCache(new \Zend_Cache_Core());
        if (IndexManager::hasDefaultCache()) {
            IndexManager::getDefaultCache();
        }
    }

    public function testGetAndSetDefaultCache()
    {
        $cache = new \Zend_Cache_Core();
        IndexManager::setDefaultCache($cache);
        $this->assertSame($cache, IndexManager::getDefaultCache());
    }

    public function testFactory()
    {
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
        $indexManager = IndexManager::factory();
        $this->assertTrue(file_exists(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/segments.gen'));
    }

    public function testFactoryParamsOverrideDefaults()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        @unlink(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/segments.gen');
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
        $indexManager = IndexManager::factory(null, array(
            IndexManager::PARAM_CREATE_IF_NOT_EXISTS => false
        ));
    }

    public function testFactoryParamUseDefaultPath()
    {
        $this->deleteAll(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/useDefaultPath');
        mkdir(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/useDefaultPath', 0777, true);
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/useDefaultPath');
        $indexManager = IndexManager::factory('true', array(
            IndexManager::PARAM_USE_DEFAULT_PATH => true
        ));
        $this->assertTrue(file_exists(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/useDefaultPath/true/segments.gen'));
    }

    public function testFactoryWithNoIndexPathAndNoDefaultIndexPathThrowsException()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        IndexManager::setDefaultIndexPath(null);
        $indexManager = IndexManager::factory(null, array(
            IndexManager::PARAM_USE_DEFAULT_PATH => true
        ));
    }

    public function testFactoryOpensExistingIndex()
    {
        IndexManager::setDefaultIndexPath(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest');
        $indexManager = IndexManager::factory();
        $this->assertTrue(file_exists(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/segments.gen'));

        $indexManager = IndexManager::factory();
        $this->assertTrue(file_exists(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/segments.gen'));
    }

    public function testGetAndSetCache()
    {
        $cache = new \Zend_Cache_Core();
        $indexManager = IndexManager::factory();
        $a = $indexManager->setCache($cache);

        $this->assertSame($cache, $indexManager->getCache());
        $this->assertSame($a, $indexManager);
    }

    public function testGetCacheUsesDefaultCache()
    {
        $cache = new \Zend_Cache_Core();
        IndexManager::setDefaultCache($cache);

        $indexManager = IndexManager::factory();

        $this->assertSame($cache, $indexManager->getCache());
    }

    public function testDelete()
    {
        $indexManager = IndexManager::factory();

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::text($indexManager->getIdKey(), 'test'));
        $indexManager->getSearchIndex()->addDocument($document);
        
        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::text($indexManager->getIdKey(), 'test'));
        $indexManager->getSearchIndex()->addDocument($document);

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::text($indexManager->getIdKey(), 'test2'));
        $indexManager->getSearchIndex()->addDocument($document);

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::text('zsl', 'other'));
        $indexManager->getSearchIndex()->addDocument($document);

        $indexManager->getSearchIndex()->commit();
        
        $results = $indexManager->search('test');
        $this->assertEquals(3, $results->getAdapter()->count());

        $results = $indexManager->search('other');
        $this->assertEquals(1, $results->getAdapter()->count());

        $a = $indexManager->delete('test');
        $this->assertSame($indexManager, $a);

        // Removes test2 also, not strict searching
        $results = $indexManager->search('test');
        $this->assertEquals(0, $results->getAdapter()->count());

        $a = $indexManager->delete('other', 'zsl');
        $this->assertSame($indexManager, $a);

        $results = $indexManager->search('other');
        $this->assertEquals(0, $results->getAdapter()->count());
    }

    public function testGetDocumentIds()
    {
        $indexManager = IndexManager::factory();

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::keyword($indexManager->getIdKey(), 'test'));
        $indexManager->getSearchIndex()->addDocument($document);

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::keyword($indexManager->getIdKey(), 'test'));
        $indexManager->getSearchIndex()->addDocument($document);

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::keyword($indexManager->getIdKey(), 'test2'));
        $indexManager->getSearchIndex()->addDocument($document);

        $document = new \Zend_Search_Lucene_Document();
        $document->addField(\Zend_Search_Lucene_Field::keyword('zsl', 'other'));
        $indexManager->getSearchIndex()->addDocument($document);

        $indexManager->getSearchIndex()->commit();

        $results = $indexManager->search('test');
        $this->assertEquals(2, $results->getAdapter()->count());

        $results = $indexManager->search('test*');
        $this->assertEquals(3, $results->getAdapter()->count());

        $results = $indexManager->search('other');
        $this->assertEquals(1, $results->getAdapter()->count());

        $docIds = $indexManager->getDocumentIds('test');
        $this->assertContains(0, $docIds);
        $this->assertContains(1, $docIds);

        $results = $indexManager->getDocumentIds('other', 'zsl');
        $this->assertContains(3, $results);
    }

    public function testGetAndSetIdKey()
    {
        $indexManager = IndexManager::factory();

        $this->assertEquals('zsl_record_id', $indexManager->getIdKey());

        $indexManager->setIdKey('test');

        $this->assertEquals('test', $indexManager->getIdKey());
    }

    public function testGetSearchIndex()
    {
        $indexManager = IndexManager::factory();
        $this->assertType('Zend_Search_Lucene_Interface', $indexManager->getSearchIndex());
    }

    public function testIndex()
    {
        $this->indexable = false;
        $indexManager = IndexManager::factory();
        $indexManager->index($this);

        $this->assertEquals(0, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());

        $indexManager->index(array($this, $this, $this));

        $this->assertEquals(0, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());
        $this->indexable = true;
    }

    public function testIndexIgnoresNonIndexableStatus()
    {
        $indexManager = IndexManager::factory();
        $indexManager->index($this);

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());

        $indexManager->index(array($this, $this, $this));

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());
    }

    public function testIndexIgnoresNonIndexableStatusDeletesExistingRecord()
    {
        $indexManager = IndexManager::factory();
        $indexManager->index($this);

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());

        $indexManager->index(array($this, $this, $this));

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());


        $this->indexable = false;

        $indexManager->index(array($this, $this, $this));

        $this->assertEquals(0, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());
        $this->indexable = true;
    }

    public function testIndexThrowsExceptionOnInvalidInterface()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        $indexManager = IndexManager::factory();
        $indexManager->index($indexManager);
    }

    public function testIndexThrowsExceptionOnInvalidInterfaceWhenArray()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        $indexManager = IndexManager::factory();
        $indexManager->index(array($this, $indexManager));
    }

    public function testIndexAddsDocsInsteadOfUpdate()
    {
        $indexManager = IndexManager::factory();
        $indexManager->index($this);

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());

        $indexManager->index(array($this, $this), false);

        $this->assertEquals(3, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());
    }

    public function testIndexThrowsExceptionOnInvalidDocId()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        $entity = $this->getMock('Core\Engine\Search\Lucene\IndexableInterface', array('getSearchDocumentId', 'getSearchDocument', 'isSearchIndexable'));
        $entity->expects($this->any())
               ->method('getSearchDocumentId')
               ->withAnyParameters();

        $entity->expects($this->any())
               ->method('isSearchIndexable')
               ->withAnyParameters()
               ->will($this->returnValue(true));
        
        $indexManager = IndexManager::factory();
        $indexManager->index($entity);
    }

    public function testIndexThrowsExceptionOnInvalidDoc()
    {
        $this->setExpectedException('Core\Engine\Search\Lucene\Exception');
        $entity = $this->getMock('Core\Engine\Search\Lucene\IndexableInterface', array('getSearchDocumentId', 'getSearchDocument', 'isSearchIndexable'));

        $entity->expects($this->any())
               ->method('getSearchDocumentId')
               ->will($this->returnValue('test'));
        
        $entity->expects($this->any())
               ->method('isSearchIndexable')
               ->withAnyParameters()
               ->will($this->returnValue(true));

        $indexManager = IndexManager::factory();
        $indexManager->index($entity);
    }

    public function testSearch()
    {
        $indexManager = IndexManager::factory();
        $indexManager->index($this);

        $this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());

        $results = $indexManager->search('value');
        $this->assertEquals(1, $results->getAdapter()->count());
    }

//    public function testSearchReturnsCachedResults()
//    {
//        @mkdir(PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/cache', 0777, true);
//        $cache = \Zend_Cache::factory('Core', 'File', array(
//            'automatic_serialization' => true
//        ), array(
//            'cache_dir' => PATH_PROJECT . '/tests/unit/data/indexes/IndexManagerTest/cache'
//        ));
//
//        $indexManager = IndexManager::factory();
//        $indexManager->setCache($cache);
//        $indexManager->index($this);
//
//        //$this->assertEquals(1, $indexManager->search($this->getSearchDocumentId())->getAdapter()->count());
//
//        $results = $indexManager->search('value');
//        //$this->assertEquals(1, $results->getAdapter()->count());
//
//        $results = $indexManager->search('value');
//        //$this->assertEquals(1, $results->getAdapter()->count());
//    }

    public function getSearchDocumentId()
    {
        return 'testing';
    }

    public function getSearchDocument()
    {
        $doc = new \Zend_Search_Lucene_Document();
        $doc->addField(\Zend_Search_Lucene_Field::keyword('zsl_record_id', $this->getSearchDocumentId()));
        $doc->addField(\Zend_Search_Lucene_Field::text('test', 'value'));
        return $doc;
    }

    public function isSearchIndexable()
    {
        return $this->indexable;
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