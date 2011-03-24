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
 * IndexManager Interface
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
interface IndexManagerInterface
{
    /**
     * Whether the default cache has been set
     *
     * @return boolean
     */
    public static function hasDefaultCache();

    /**
     * Get default cache
     *
     * @return Zend_Cache_Core
     */
    public static function getDefaultCache();

    /**
     * Set the result cache
     *
     * @param \Zend_Cache_Core $cache
     */
    public static function setDefaultCache(\Zend_Cache_Core $cache);

    /**
     * Get cache
     *
     * @return \Zend_Cache_Core
     */
    public function getCache();

    /**
     * Set cache object
     *
     * @param \Zend_Cache_Core $cache
     * @return IndexManagerInterface
     */
    public function setCache(\Zend_Cache_Core $cache);

    /**
     * Remove a record from the search index
     *
     * @param string $value
     * @param string $searchField
     * @return IndexManagerInterface
     */
    public function delete($value, $searchField = null);

    /**
     * Get the Lucene document IDs by search the specified search field.
     * If no search field is specified, the default ID field is used.
     *
     * @param string $value
     * @param string $searchField
     * @return array
     */
    public function getDocumentIds($value, $searchField = null);

    /**
     * Get the ID key
     *
     * @return void
     */
    public function getIdKey();

    /**
     * Set the ID key
     *
     * @return IndexManager
     */
    public function setIdKey($idKey);

    /**
     * Get the search index
     *
     * @return mixed
     */
    public function getSearchIndex();

    /**
     * Index an IndexableInterface
     *
     * @throws Exception
     * @param  IndexableInterface|array $indexables
     * @param  boolean                  $update
     * @param  string                   $searchField
     * @return IndexManagerInterface
     */
    public function index($indexables, $update = true, $searchField = null);

    /**
     * Execute the query
     *
     * @param mixed $query
     * @return array
     */
    public function search($query);
}