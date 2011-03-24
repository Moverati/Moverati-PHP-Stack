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
class IndexManager implements IndexManagerInterface
{
    /**
     * Use default index path
     *
     */
    const PARAM_USE_DEFAULT_PATH = 'useDefaultPath';

    /**
     * Create index if not exists
     *
     */
    const PARAM_CREATE_IF_NOT_EXISTS = 'createIfNotExists';

    /**
     * Index class
     *
     */
    const PARAM_INDEX_CLASS = 'indexClass';

    /**
     * Default index class
     *
     * @var string
     */
    private static $defaultIndexClass = 'Core\Engine\Search\Lucene\IndexManager';

    /**
     * Default path for the search index.
     * Usefull when the application has just one search index.
     *
     * Only used if set.
     *
     * @var string
     */
    private static $defaultIndexPath = '.';

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
    private static $defaultIdKey = 'zsl_record_id';

    /**
     * Record ID key
     *
     * @var string
     */
    private $idKey;

    /**
     * The search index
     *
     * @var \Zend_Search_Lucene_Interface
     */
    private $searchIndex;

    /**
     * Cache
     *
     * @var \Zend_Cache_Core
     */
    private $cache;

    /**
     * Get default index class
     *
     * @return string
     */
    public static function getDefaultIndexClass()
    {
        return static::$defaultIndexClass;
    }

    /**
     * Set the default index class
     *
     * @param string $path
     */
    public static function setDefaultIndexClass($class)
    {
        static::$defaultIndexClass = $class;
    }

    /**
     * Get default index path
     *
     * @return string
     */
    public static function getDefaultIndexPath()
    {
        return static::$defaultIndexPath;
    }

    /**
     * Set the default index path
     *
     * @param string $path
     */
    public static function setDefaultIndexPath($path)
    {
        static::$defaultIndexPath = $path;
    }

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
     * Get a Zend_Search_Lucene_Index instance
     *
     * @param string $indexPath
     * @param array  $params
     * @return IndexManager
     */
    public static function factory($indexPath = null, array $params = array())
    {
        $defaultParams = array(
            static::PARAM_USE_DEFAULT_PATH     => true,
            static::PARAM_CREATE_IF_NOT_EXISTS => true,
            static::PARAM_INDEX_CLASS          => static::getDefaultIndexClass()
        );

        $params            = array_merge($defaultParams, $params);

        $useDefaultPath    = $params[static::PARAM_USE_DEFAULT_PATH];
        $createIfNotExists = $params[static::PARAM_CREATE_IF_NOT_EXISTS];
        $indexClass        = $params[static::PARAM_INDEX_CLASS];


        if (! $indexPath && ! static::$defaultIndexPath) {
            throw new Exception('No index path specified');
        }


        $indexPath = rtrim($indexPath, '/\\');
        if ($useDefaultPath) {
            $indexPath = rtrim(static::getDefaultIndexPath(), '/\\') . '/' . ltrim($indexPath, '/\\');
        }

        if (file_exists($indexPath . '/segments.gen')) {
            $index = new $indexClass(\Zend_Search_Lucene::open($indexPath));
        } else {
            if (! $createIfNotExists) {
                throw new Exception(
                    'Index "' . $indexPath . '" does not exists'
                );
            }

            $index = new $indexClass(\Zend_Search_Lucene::create($indexPath));
        }

        return $index;
    }

    /**
     * Construct the indexer
     *
     * @param Zend_Search_Lucene_Interface $index
     * @param string $idKey
     */
    public function __construct(\Zend_Search_Lucene_Interface $searchIndex, $idKey = null)
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
        $documentIds = $index->find($searchField . ': +"' . $value . '"');
        foreach ($documentIds as $id) {
            $index->delete($id->id);
        }

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

        $term   = new \Zend_Search_Lucene_Index_Term($value, $searchField);
        $docIds = $this->getSearchIndex()->termDocs($term);

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
     * @return \Zend_Search_Lucene_Interface
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

            $index->addDocument($document);
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
            $results = $this->executeSearch($query);
//        }

        $paginator = \Zend_Paginator::factory($results);
        return $paginator;
    }

    /**
     * Execute the search and return the results
     *
     * @param mixed
     * @return array
     */
    protected function executeSearch($query)
    {
        return $this->getSearchIndex()->find($query);
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