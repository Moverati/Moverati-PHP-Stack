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
 * IndexManager
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SolrIndexManager implements IndexManagerInterface
{
    /**
     * Result cache
     *
     * @var \Zend_Cache_Core
     */
    private static $defaultCache;

    /**
     * Default record ID key
     *
     * @var string
     */
    private static $defaultIdKey = 'id';

    /**
     * Record ID key
     *
     * @var string
     */
    private $idKey;

    /**
     * The search index
     *
     * @var \Apache_Solr_Service
     */
    private $searchIndex;

    /**
     * Cache
     *
     * @var \Zend_Cache_Core
     */
    private $cache;

    /**
     * Whether the default cache has been set
     *
     * @return boolean
     */
    public static function hasDefaultCache()
    {
        return isset(static::$defaultCache);
    }

    /**
     * Get default cache
     *
     * @return Zend_Cache_Core
     */
    public static function getDefaultCache()
    {
        if (! static::$defaultCache instanceof \Zend_Cache_Core) {
            throw new Exception('Default cache object is not set');
        }

        return static::$defaultCache;
    }

    /**
     * Set the result cache
     *
     * @param \Zend_Cache_Core $cache
     */
    public static function setDefaultCache(\Zend_Cache_Core $cache)
    {
        static::$defaultCache = $cache;
    }

    /**
     * Construct the indexer
     *
     * @param \Apache_Solr_Service $searchIndex
     * @param string $idKey
     */
    public function __construct(\Apache_Solr_Service $searchIndex, $idKey = null)
    {
        if ($idKey === null) {
            $idKey = static::$defaultIdKey;
        }

        $this->searchIndex = $searchIndex;
        $this->idKey       = $idKey;
    }

    /**
     * Get cache
     *
     * @return \Zend_Cache_Core
     */
    public function getCache()
    {
        if (! $this->cache instanceof \Zend_Cache_Core && static::hasDefaultCache()) {
            $this->setCache(static::getDefaultCache());
        }

        return $this->cache;
    }

    /**
     * Set cache object
     *
     * @param \Zend_Cache_Core $cache
     * @return IndexManager
     */
    public function setCache(\Zend_Cache_Core $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Remove a record from the search index
     *
     * @param string $value
     * @param string $searchField
     * @return IndexManager
     */
    public function delete($value, $searchField = null)
    {
        if ($searchField === null) {
            $searchField = $this->getIdKey();
        }

        $index       = $this->getSearchIndex();
        $documentIds = $index->deleteByQuery($searchField . ': (+"' . $value . '")');

        return $this;
    }

    /**
     * Get the Lucene document IDs by search the specified search field.
     * If no search field is specified, the default ID field is used.
     *
     * @param string $value
     * @param string $searchField
     * @return array
     */
    public function getDocumentIds($value, $searchField = null)
    {
        if ($searchField === null) {
            $searchField = $this->getIdKey();
        }

        $docIds = $this->getSearchIndex()->terms($value, $searchField, 50);

        return $docIds;
    }

    /**
     * Get the ID key
     *
     * @return void
     */
    public function getIdKey()
    {
        return $this->idKey;
    }

    /**
     * Set the ID key
     *
     * @return IndexManager
     */
    public function setIdKey($idKey)
    {
        $this->idKey = $idKey;
        return $this;
    }

    /**
     * Get the search index
     *
     * @return \Apache_Solr_Service
     */
    public function getSearchIndex()
    {
        return $this->searchIndex;
    }

    /**
     * Index an IndexableInterface
     *
     * @throws Exception
     * @param  IndexableInterface|array $indexables
     * @param  boolean                  $update
     * @param  string                   $searchField
     * @return IndexManager
     */
    public function index($indexables, $update = true, $searchField = null)
    {
        if ($searchField === null) {
            $searchField = $this->getIdKey();
        }

        if (!is_array($indexables)) {
            $indexables = array($indexables);
        }

        $index = $this->getSearchIndex();

        foreach ($indexables as $indexable) {
            if (! $indexable instanceof IndexableInterface) {
                throw new Exception(
                	'The object of type "' . get_class((object)$indexable) . '" ' . 'is not an instance of Core\Engine\Search\Lucene\IndexableInterface.'
                );
            }

            // Ignore non-searchable state
            if (!$indexable->isSearchIndexable()) {
                $docId = $indexable->getSearchDocumentId();
                if (! $docId) {
                    throw new Exception('You must provide a valid document ID.');
                }
                $this->delete($docId, $searchField);
                continue;
            }

            if ($update) {
                $docId = $indexable->getSearchDocumentId();
                if (! $docId) {
                    throw new Exception('You must provide a valid document ID.');
                }
                $this->delete($docId, $searchField);
            }

            $document = $indexable->getSearchDocument();
            if (! $document instanceof \Zend_Search_Lucene_Document) {
                throw new Exception(
                	'The provided search-document is not ' . 'an instance of Zend_Search_Lucene_Document.'
                );
            }

            // Unique document id
            if (!in_array($searchField, $document->getFieldNames())) {
                $document->addField(
                    \Zend_Search_Lucene_Field::keyword($searchField, $indexable->getSearchDocumentId())
                );
            }

            // Class
            if (!in_array('class', $document->getFieldNames())) {
                $document->addField(
                    \Zend_Search_Lucene_Field::text('class', get_class($indexable))
                );
            }

            $solrDocument = new \Apache_Solr_Document();
            if ($document->boost !== 1.0) {
                $solrDocument->setBoost($document->boost);
            }

            foreach ($document->getFieldNames() as $name) {
                /* @var $field \Zend_Search_Lucene_Field */
                $field = $document->getField($name);

                if ($field->boost !== 1.0) {
                    $solrDocument->addField($field->name, $field->value, $field->boost);
                } else {
                    $solrDocument->addField($field->name, $field->value);
                }
            }

            $index->addDocument($solrDocument);
        }

        $index->commit();
        //$index->optimize();

        return $this;
    }

    /**
     * Execute the query
     *
     * @param mixed $query
     * @return array
     */
    public function search($query)
    {
//        $cache = $this->getCache();
//        if ($cache instanceof \Zend_Cache_Core) {
//            $queryHash = $this->getQueryHash($query);
//
//            // Try to load from cache
//            if (! ($results = $cache->load($queryHash))) {
//                $results = $this->executeSearch($query);
//
//                $cache->save($results);
//            }
//            return $results;
//        } else {
//            $results = $this->executeSearch($query);
//        }

        $solrService = $this->getSearchIndex();
        $paginator   = new \Zend_Paginator(new Solr\SolrPaginationAdapter($solrService, $query));
        return $paginator;
    }

    /**
     * Get the query hash
     *
     * @param mixed $query
     * @return string
     */
    protected function getQueryHash($query)
    {
        return md5($query);
    }
}