<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;

/**
 * Abstract Class CollectionModel
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class CollectionModel extends Model
{
    /**
     * @var array IDs.
     */
    private $_ids;

    /**
     * @var callable Class for collection.
     */
    private $_class;

    /**
     * @var int Number of models in collection.
     */
    private $_num;

    /**
     * CollectionModel constructor.
     *
     * @param callable $class The class to be generated as a collection.
     * @param string $table The table to gather collection from.
     * @param string $limitClause Full LIMIT clause.
     * @param string $offsetClause Full OFFSET clause.
     * @param string $orderClause Full ORDER BY clause.
     * @param string $whereClause Full WHERE clause.
     *
     * @throws MySQLQueryException
     */
    function __construct($class, string $table, $limitClause = null, $offsetClause = null,
                         $orderClause = null, $whereClause = null)
    {
        parent::__construct();
        try {
            $this->loadIDs($table, $limitClause, $offsetClause, $orderClause, $whereClause);
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
        $this->_class = $class;
    }

    /**
     *
     *
     * @param string $table
     * @param $limitClause
     * @param $offsetClause
     * @param $orderClause
     * @param $whereClause
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    private function loadIDs(string $table, $limitClause, $offsetClause,
                             $orderClause, $whereClause) {
        $table = mysqli_real_escape_string($this->db, $table);
        $limitClause = mysqli_real_escape_string($this->db, $limitClause);
        $offsetClause = mysqli_real_escape_string($this->db, $offsetClause);
        $orderClause = mysqli_real_escape_string($this->db, $orderClause);
        $whereClause = mysqli_real_escape_string($this->db, $whereClause);
        if (!$result = $this->db->query(
            "SELECT id 
            FROM $table 
            $whereClause 
            $orderClause 
            $limitClause 
            $offsetClause;"
        )) {
            throw new MySQLQueryException('Error from select in CollectionModel::__construct');
        }
        $this->_ids = array_column($result->fetch_all(), 0);
        $this->_num = $result->num_rows;
    }

    /**
     * @return int Number of models in collection.
     */
    public function getNum()
    {
        return $this->_num;
    }

    /**
     * Get collection of models
     *
     * @return Generator|Model[] model objects
     */
    public function getObjects()
    {
        foreach ($this->_ids as $id) {
            yield (new $this->_class())->load($id);
        }
    }
}
