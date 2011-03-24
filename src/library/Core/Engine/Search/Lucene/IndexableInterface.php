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
 * IndexableInterface
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
interface IndexableInterface
{
   /**
    * Returns the unique identifier for the search index
    *
    * @return string
    */
   public function getSearchDocumentId();

   /**
    * Gets a complete search document used for indexing.
    *
    * @return \Zend_Search_Lucene_Document
    */
   public function getSearchDocument();

   /**
    * Whether or not this object should be indexed
    *
    * @return boolean
    */
   public function isSearchIndexable();
}