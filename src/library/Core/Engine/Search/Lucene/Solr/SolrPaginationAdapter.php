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

namespace Core\Engine\Search\Lucene\Solr;

use Core\Engine;

/**
 * Solr pagination adapter
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SolrPaginationAdapter implements \Zend_Paginator_Adapter_Interface
{
    /**
     * Solr
     *
     * @var \Apache_Solr_Service
     */
    private $solrService;

    /**
     * Solr Query
     *
     * @var string
     */
    private $query;

    /**
     * Solr Query params
     *
     * @var array
     */
    private $params = array();

    /**
     * Document Count
     *
     * @var integer
     */
    private $docCount;

    /**
     * Construct
     *
     * @param \Apache_Solr_Service $solrService
     * @param string $query
     */
    public function __construct(\Apache_Solr_Service $solrService, $query, array $params = array())
    {
        $this->solrService = $solrService;
        $this->query       = $query;
    }


    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $solr    = $this->getSolrService();
        $query   = $this->getQuery();
        $params  = $this->getParams();
        $results = array();

        $response = $solr->search($query, $offset, $itemCountPerPage, $params);

        $luceneMock     = new LuceneMock();
        $this->docCount = $response->response->numFound;

        foreach ($response->response->docs as $doc) {
            /* @var $doc \Apache_Solr_Document */

            $luceneDocument = new \Zend_Search_Lucene_Document();
            foreach ($doc->getFieldNames() as $fieldName) {
                $fieldInfo = $doc->getField($fieldName);

                $field = \Zend_Search_Lucene_Field::text($fieldName, $fieldInfo['value']);
                $luceneDocument->addField($field);
            }

            $queryHit = new QueryHit($luceneMock);
            $queryHit->id    = $doc->id;
            $queryHit->setDocument($luceneDocument);
            $results[] = $queryHit;
        }

        return $results;
    }


    /**
     * @return int
     */
    public function count()
    {
        if (is_null($this->docCount)) {
            $solr    = $this->getSolrService();
            $query   = $this->getQuery();
            $params  = $this->getParams();
            $results = array();

            $response = $solr->search($query, 0, 1, $params);

            $this->docCount = $response->response->numFound;
        }
        
        return $this->docCount;
    }


    /**
     * Get the solr service
     *
     * @return \Apache_Solr_Service
     */
    public function getSolrService()
    {
        return $this->solrService;
    }

    /**
     * Get the query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the query params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}