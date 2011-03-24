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

use Core\Engine\Search\Lucene;

/**
 * Mock Entity
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 *
 * @Entity
 */
class MockEntity implements Lucene\IndexableInterface
{
    /**
     * Id
     * 
     * @var integer
     * 
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Gets the ID of the User
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns the unique identifier for the search index
     *
     * @return string
     */
    public function getSearchDocumentId()
    {
        return md5(get_class($this) . $this->getId());
    }

    /**
     * Gets a complete search document used for indexing.
     *
     * @return \Zend_Search_Lucene_Document
     */
    public function getSearchDocument()
    {
        $document = new \Zend_Search_Lucene_Document();

        $idField = \Zend_Search_Lucene_Field::keyword('id', $this->getSearchDocumentId());
        $document->addField($idField);

        return $document;
    }
    
    /**
    * Whether or not this object should be indexed
    *
    * @return boolean
    */
    public function isSearchIndexable()
    {
        return true;
    }
}