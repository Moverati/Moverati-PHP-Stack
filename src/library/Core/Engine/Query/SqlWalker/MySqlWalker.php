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

namespace Core\Engine\Query\SqlWalker;

use Doctrine\ORM\Query\SqlWalker;

/**
 * MySql Walker
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class MySqlWalker extends SqlWalker
{
    const SQL_CALC_FOUND_ROWS = 'mySqlWalker.sqlCalcFoundRows';
    const SQL_NO_CACHE        = 'mySqlWalker.sqlNoCache';

    const USE_INDEX           = 'mySqlWalker.useIndex';
    const FORCE_INDEX         = 'mySqlWalker.forceIndex';
    const IGNORE_INDEX        = 'mySqlWalker.ignoreIndex';

     /**
     * Walks down a SelectClause AST node, thereby generating the appropriate SQL.
     *
     * @param $selectClause
     * @return string The SQL.
     */
    public function walkSelectClause($selectClause)
    {
        $sql   = parent::walkSelectClause($selectClause);
        $query = $this->getQuery();
        if ($query->getHint(self::SQL_NO_CACHE) === true) {
            if ($selectClause->isDistinct) {
                $sql = str_replace('SELECT DISTINCT', 'SELECT DISTINCT SQL_NO_CACHE', $sql);
            } else {
                $sql = str_replace('SELECT', 'SELECT SQL_NO_CACHE', $sql);
            }
        }

        return $sql;
    }


    /**
     * Walks down a FromClause AST node, thereby generating the appropriate SQL.
     *
     * @return string The SQL.
     */
    public function walkFromClause($fromClause)
    {
        $sql   = parent::walkFromClause($fromClause);
        $query = $this->getQuery();

        if ($query->getHint(self::USE_INDEX)) {
           $sql .= ' USE INDEX (' . implode(',', (array) $query->getHint(self::USE_INDEX)) . ')';
        }


        if ($query->getHint(self::FORCE_INDEX)) {
            $sql .= 'FORCE INDEX (' . implode(',', (array) $query->getHint(self::FORCE_INDEX)) . ')';
        }

        if ($query->getHint(self::IGNORE_INDEX)) {
            $sql .= 'IGNORE INDEX (' . implode(',', (array) $query->getHint(self::USE_INDEX)) . ')';
        }

        return $sql;
    }
}