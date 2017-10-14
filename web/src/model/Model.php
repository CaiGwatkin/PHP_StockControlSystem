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
        $this->db->query("CREATE DATABASE IF NOT EXISTS ".DB_NAME.";");

        if (!$this->db->select_db(DB_NAME)) {
            throw new MySQLDatabaseException('MySQL database not available');
        }

        $result = $this->db->query("SHOW TABLES LIKE 'user';");
        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it and populate with sample data

            $result = $this->db->query(
                    "CREATE TABLE user (
                        id int(8) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
                        username VARCHAR(256) NOT NULL UNIQUE,
                        pwd VARCHAR(256) NOT NULL,
                        name VARCHAR(256) NOT NULL,
                        PRIMARY KEY (id)
            );");
            if (!$result) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed creating table: user');
            }
            // Add sample data, password is hashed on combination of ID and inputted password
            $pwd1 = password_hash('1'.'admin', PASSWORD_DEFAULT);
            $pwd2 = password_hash('2'.'bobbyBOB', PASSWORD_DEFAULT);
            $pwd3 = password_hash('3'.'maryMARY', PASSWORD_DEFAULT);
            $pwd4 = password_hash('4'.'joeyJOEY', PASSWORD_DEFAULT);
            if(!$this->db->query(
                    "INSERT INTO user
                    VALUES (NULL,'admin','$pwd1','Admin'),
                        (NULL,'bob','$pwd2','Bob'),
                        (NULL,'mary','$pwd3','Mary'),
                        (NULL,'joe','$pwd4','Joe');"
            )) {
                error_log($this->db->error);
                throw new MySQLDatabaseException('Failed adding sample data to table: user');
            }
        }

//        $result = $this->db->query("SHOW TABLES LIKE 'transfer';");
//        if ($result->num_rows == 0) {
//            // table doesn't exist
//            // create it and populate with sample data
//
//            $result = $this->db->query(
//                    "CREATE TABLE transfer (
//                        id int(8) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
//                        datetimeOf DATETIME NOT NULL,
//                        valueOf DECIMAL(19,2) unsigned NOT NULL,
//                        fromAccount int(8) unsigned NOT NULL,
//                        toAccount int(8) unsigned NOT NULL,
//                        PRIMARY KEY (id),
//                        FOREIGN KEY (fromAccount) REFERENCES user_account(id) ON DELETE CASCADE,
//                        FOREIGN KEY (toAccount) REFERENCES user_account(id) ON DELETE CASCADE
//            );");
//            if (!$result) {
//                error_log($this->db->error);
//                throw new MySQLDatabaseException('Failed creating table: transfer');
//            }
//            // Add sample data, password is hashed on combination of ID and inputted password
//            $date1 = date("Y-m-d H:i:s");
//            $date2 = date("Y-m-d H:i:s");
//            $date3 = date("Y-m-d H:i:s");
//            $date4 = date("Y-m-d H:i:s");
//            if(!$this->db->query(
//                    "INSERT INTO transfer
//                    VALUES (NULL,'$date1',20.00,2,3),
//                        (NULL,'$date2',5.00,3,4),
//                        (NULL,'$date3',8.00,3,2),
//                        (NULL,'$date4',2.00,4,2);"
//            )) {
//                error_log($this->db->error);
//                throw new MySQLDatabaseException('Failed adding sample data to table: transfer');
//            }
//        }
        //----------------------------------------------------------------------------

    }
}
