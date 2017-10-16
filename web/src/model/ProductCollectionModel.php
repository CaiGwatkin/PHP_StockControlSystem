<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;

/**
 * Class ProductCollectionModel
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class ProductCollectionModel extends CollectionModel
{
    /**
     * ProductCollectionModel constructor.
     *
     * Sends 'product' as table to parent constructor.
     *
     * @param string $needle Needle of name to be filtered on.
     * @param string $haystack The column to find needle in.
     * @param string $orderBy Column to order results by.
     * @param string $sort ASC or DESC.
     *
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    function __construct($needle = null, $haystack = null, $orderBy = null, $sort = null)
    {
        $table = 'product';
        $sort = $sort == 'ASC' || $sort == 'DESC' ? $sort : null;
        $needle = "%$needle%";
        try {
            parent::__construct(ProductModel::class, $table, $needle, $haystack, $orderBy, $sort);
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }
}
