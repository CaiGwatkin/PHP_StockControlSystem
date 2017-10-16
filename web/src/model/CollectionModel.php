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
     * CollectionModel constructor.
     *
     * @param callable $class The class to be generated as a collection.
     * @param string $table The table to gather collection from.
     * @param string $needle Needle value for WHERE INSTR(needle, haystack).
     * @param string $haystack Haystack value for WHERE INSTR(needle, haystack).
     * @param string $orderBy Order by value.
     * @param string $sort Sort order.
     *
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    function __construct($class, string $table, $needle = null, $haystack = null, $orderBy = null, $sort = null)
    {
        parent::__construct();
        try {
            $this->loadIDs($table, $needle, $haystack, $orderBy, $sort);
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
        $this->_class = $class;
    }

    /**
     * Load IDs from database
     *
     * @param string $table The table to load from.
     * @param string $needle Needle for WHERE INSTR(needle, haystack).
     * @param string $haystack Haystack for WHERE INSTR(needle, haystack).
     * @param string $orderBy Order by value.
     * @param string $sort Sort order.
     *
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    private function loadIDs(string $table, $needle, $haystack, $orderBy, $sort) {
        // Extremely messy workaround for prepared statements not letting columns names be added as params
        $query = "SELECT id FROM $table";
        if ($needle && $haystack) {
            $haystack = $this->db->real_escape_string($haystack);
            $query = $query." WHERE $haystack LIKE ?";
        }
        if ($orderBy) {
            $query = $query." ORDER BY $orderBy";
            if ($sort) {
                $query = $query." $sort";
            }
        }
        if (!($stmt = $this->db->prepare($query))) {
            throw new MySQLIStatementException('Error in prepare() in CollectionModel::loadIDs');
        }
        if ($needle && $haystack && !($stmt->bind_param('s', $needle))) {
            throw new MySQLIStatementException('Error in bind_param() in CollectionModel::loadIDs');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in CollectionModel::loadIDs');
        }
        if (!($result = $stmt->get_result())) {
            throw new MySQLIStatementException('Error in get_result() in CollectionModel::loadIDs');
        }
        $this->_ids = array_column($result->fetch_all(), 0);
        $stmt->close();
    }

    /**
     * Get collection of models
     *
     * @return Generator|Model[] model objects
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    public function getObjects()
    {
        try {
            foreach ($this->_ids as $id) {
                yield (new $this->_class())->load($id);
            }
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }
}
