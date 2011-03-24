<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace DoctrineExtensions\MySqlPaginate;

use Doctrine\ORM\Query;

/**
 * Implements the Zend_Paginator_Adapter_Interface for use with Zend_Paginator
 *
 * Depends on MySQL's LIMIT/OFFSET statements and simplifies the task of having
 * to build a count query.
 *
 * @author      Daniel Cousineau
 * @category    DoctrineExtensions
 * @package     DoctrineExtensions\MySqlPaginate
 * @copyright   Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class PaginationAdapter implements \Zend_Paginator_Adapter_Interface
{
    /**
     * The SELECT query to paginate
     *
     * @var Query
     */
    protected $query = null;

    /**
     * The SELECT query results
     *
     * @var array
     */
    protected $results = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     * Constructor
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Sets the total row count for this paginator
     *
     * @param integer $rowCount
     * @return void
     */
    public function setRowCount($rowCount)
    {
        if (is_integer($rowCount)) {
            $this->rowCount = $rowCount;
        } else {
            throw new \InvalidArgumentException("Invalid row count");
        }
    }

    /**
     * Gets the current page of items
     *
     * @param string $offset
     * @param string $itemCountPerPage
     * @return void
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setFirstResult($offset)
                    ->setMaxResults($itemCountPerPage);

        $this->results = $this->query->getResult();
        $this->setRowCount(count($this->results));

        return $this->results;
    }

    /**
     * @return int
     */
    public function count()
    {
        if (is_null($this->rowCount) && !is_null($this->results)) {
            $this->setRowCount(
                count($this->results)
            );
        }
        return $this->rowCount;
    }
}