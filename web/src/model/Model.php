<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

use cgwatkin\a3\exception\MySQLDatabaseException;
use mysqli;

/**
 * Class Model
 *
 * Connects to and configures the MySQL database with dummy data for testing.
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class Model
{
    protected $db;

    function __construct()
    {
        $this->db = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS
        );

        if (!$this->db) {
            throw new MySQLDatabaseException($this->db->connect_error, $this->db->connect_errno);
        }

        //----------------------------------------------------------------------------
        // Creates the database and populates it with sample data
        $this->db->query('CREATE DATABASE IF NOT EXISTS '.DB_NAME.';');

        if (!$this->db->select_db(DB_NAME)) {
            throw new MySQLDatabaseException('MySQL database not available');
        }

        $result = $this->db->query('SHOW TABLES LIKE "user";');
        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it and populate with sample data

            $result = $this->db->query(
                    'CREATE TABLE user (
                        id INT unsigned NOT NULL UNIQUE AUTO_INCREMENT,
                        username VARCHAR(256) NOT NULL UNIQUE,
                        pwd VARCHAR(256) NOT NULL,
                        name VARCHAR(256) NOT NULL,
                        PRIMARY KEY (id)
            );');
            if (!$result) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed creating table: user');
            }
            // Add sample data, password is hashed on combination of ID and inputted password
            $pwd1 = password_hash('1'.'admin', PASSWORD_DEFAULT);
            $pwd2 = password_hash('2'.'TheToolman', PASSWORD_DEFAULT);
            $pwd3 = password_hash('3'.'maryMARY', PASSWORD_DEFAULT);
            $pwd4 = password_hash('4'.'joeyJOEY', PASSWORD_DEFAULT);
            if(!$this->db->query(
                    "INSERT INTO user
                    VALUES (NULL,'admin','$pwd1','Admin'),
                        (NULL,'TheToolman','$pwd2','Tim Taylor'),
                        (NULL,'mary','$pwd3','Mary'),
                        (NULL,'joey','$pwd4','Joey');"
            )) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed adding sample data to table: user');
            }
        }

        $result = $this->db->query('SHOW TABLES LIKE "product";');
        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it and populate with sample data

            $result = $this->db->query(
                    'CREATE TABLE product (
                        id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
                        sku CHAR(6) NOT NULL UNIQUE,
                        name VARCHAR(256) NOT NULL,
                        cost DECIMAL(19,2) UNSIGNED NOT NULL,
                        category VARCHAR(40) NOT NULL,
                        stock MEDIUMINT UNSIGNED NOT NULL,
                        PRIMARY KEY (id),
                        INDEX ix_product (category)
            );');
            if (!$result) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed creating table: product');
            }
            // Add sample data
            if(!$this->db->query(
                    "INSERT INTO product
                    VALUES (NULL,'HMR000','Claw Hammer',5.95,'Hammer',21),
                        (NULL,'HMR001','Ball Pein',8.95,'Hammer',22),
                        (NULL,'HMR002','Club Hammer',6.95,'Hammer',13),
                        (NULL,'SCW000','Slot Screwdriver',9.95,'Screwdriver',13),
                        (NULL,'SCW001','Phillips Screwdriver',14.95,'Screwdriver',28),
                        (NULL,'SCW002','Pozidriv Screwdriver',14.95,'Screwdriver',40),
                        (NULL,'KNF000','Pocket Knife',24.95,'Knife',19),
                        (NULL,'KNF001','Locking Knife',24.95,'Knife',8),
                        (NULL,'KNF002','Craft Knife',12.95,'Knife',21),
                        (NULL,'TPE000','Masking Tape',8.95,'Tape',17),
                        (NULL,'TPE001','Duct Tape',12.95,'Tape',32),
                        (NULL,'TPE002','Electrical Tape',8.95,'Tape',32);"
            )) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed adding sample data to table: product');
            }
        }
        //----------------------------------------------------------------------------

    }
}
