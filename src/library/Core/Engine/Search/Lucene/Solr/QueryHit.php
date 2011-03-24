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
 * Solr Query Hit
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class QueryHit extends \Zend_Search_Lucene_Search_QueryHit
{
    /**
     * Convenience function for getting fields from the document
     * associated with this hit.
     *
     * @param string $offset
     * @return string
     */
    public function __get($offset)
    {
        return $this->getDocument()->getFieldValue($offset);
    }

    /**
     * Set the document
     *
     * @param \Zend_Search_Lucene_Document $doc
     * @return QueryHit 
     */
    public function setDocument(\Zend_Search_Lucene_Document $doc)
    {
        $this->_document = $doc;
        return $this;
    }

    /**
     * Return the document object for this hit
     *
     * @return \Zend_Search_Lucene_Document
     */
    public function getDocument()
    {
        return $this->_document;
    }
}