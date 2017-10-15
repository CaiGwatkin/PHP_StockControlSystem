<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

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
     * @param int $limit Limit of number of rows to be returned.
     * @param int $offset Offset from zero'th row.
     *
     * @throws MySQLQueryException
     */
    function __construct(/*$category = null,*/ $limit = null, $offset = null)
    {
        $table = 'product';
        $limitClause = $limit ? "LIMIT $limit" : null;
        $offsetClause = $offset ? "OFFSET $offset" : null;
        $orderClause = 'ORDER BY sku ASC';
//        $whereClause =  $category ? "WHERE category = $category" : null;
        try {
            parent::__construct(ProductModel::class, $table, $limitClause, $offsetClause,
                $orderClause);
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }
}
