<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;


/**
 * Class ProductModel
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class ProductModel extends Model
{
    /**
     * @var int Product ID.
     */
    private $_id;
    
    /**
     * @var string Product Stock Keeping Unit (SKU) code.
     */
    private $_sku;
    
    /**
     * @var string Product name.
     */
    private $_name;
    
    /**
     * @var float Product cost.
     */
    private $_cost;
    
    /**
     * @var string Product category.
     */
    private $_category;

    /**
     * @var int Product stock quantity.
     */
    private $_stockQuantity;
    
    /**
     * @return int Product ID.
     */
    public function getID()
    {
        return $this->_id;
    }
    
    /**
     * @param int $id Product ID.
     * @return ProductModel $this
     */
    private function setID(int $id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string Product SKU.
     */
    public function getSKU()
    {
        return $this->_sku;
    }

    /**
     * @param string $sku Product SKU.
     * @return ProductModel $this
     */
    private function setSKU(string $sku)
    {
        $this->_sku = $sku;
        return $this;
    }

    /**
     * @return string Product name.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name Product name.
     * @return ProductModel $this
     */
    private function setName(string $name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return int Product cost.
     */
    public function getCost()
    {
        return $this->_sku;
    }

    /**
     * @param int $cost Product cost.
     * @return ProductModel $this
     */
    private function setCost(int $cost)
    {
        $this->_cost = $cost;
        return $this;
    }

    /**
     * @return string Product category.
     */
    public function getCategory()
    {
        return $this->_sku;
    }

    /**
     * @param string $category Product category.
     * @return ProductModel $this
     */
    private function setCategory(string $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * @return int Product stock quantity.
     */
    public function getStockQuantity()
    {
        return $this->_stockQuantity;
    }

    /**
     * @param int $stockQuantity Product stock quantity.
     * @return ProductModel $this
     */
    private function setStockQuantity(int $stockQuantity)
    {
        $this->_stockQuantity = $stockQuantity;
        return $this;
    }
    
    /**
     * Loads product model from MySQL.
     *
     * @param int $id Product ID.
     * @return ProductModel $this
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    public function load(int $id)
    {
        if (!($stmt = $this->db->prepare(
            "SELECT id, sku, name, cost, category, stock
            FROM product
            WHERE id = ?;"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in ProductModel::load');
        }
        if (!($stmt->bind_param('i', $id))) {
            throw new MySQLIStatementException('Error in bind_param() in ProductModel::load');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in ProductModel::load');
        }
        if (!($result = $stmt->get_result())) {
            throw new MySQLIStatementException('Error in get_result() in ProductModel::load');
        }
        if ($result->num_rows == 0) {
            throw new MySQLQueryException("No product found with id '$id' in ProductModel::load");
        }
        $result = $result->fetch_assoc();
        return $this->setId($result['id'])
            ->setSKU($result['sku'])
            ->setName($result['name'])
            ->setCost($result['cost'])
            ->setCategory($result['category'])
            ->setStockQuantity($result['stock']);
    }
}
