<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\model;

use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;


/**
 * Class UserModel
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class UserModel extends Model
{
    /**
     * @var integer User ID.
     */
    private $_id;
    
    /**
     * @var string User username.
     */
    private $_username;
    
    /**
     * @var string User password.
     */
    private $_password;
    
    /**
     * @return int User ID
     */
    public function getID()
    {
        return $this->_id;
    }
    
    /**
     * @param int $id User ID.
     * @return UserModel $this
     */
    private function setID(int $id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string User Name
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param string $_username User name
     *
     * @return UserModel $this
     */
    public function setUsername(string $_username)
    {
        $this->_username = $_username;

        return $this;
    }
    
    /**
     * @param string $password User password
     * @return UserModel $this
     */
    public function setPassword(string $password)
    {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Checks that login details are valid.
     *
     * @param string $username The username for the account to be logged in.
     * @param string $password The password.
     *
     * @return $this UserModel
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    public function checkLogin(string $username, string $password)
    {
        if (!($stmt = $this->db->prepare(
            "SELECT id
            FROM user
            WHERE username = ?"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in UserModel::checkLogin');
        }
        if (!($stmt->bind_param('s', $username))) {
            throw new MySQLIStatementException('Error in bind_param() in UserModel::checkLogin');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in UserModel::checkLogin');
        }
        if (!($result = $stmt->get_result())) {
            throw new MySQLIStatementException('Error in get_result() in UserModel::checkLogin');
        }
        if ($result->num_rows == 0) {
            throw new MySQLQueryException("No account found with username '$username' in UserModel::load");
        }
        $result = $result->fetch_assoc();
        try {
            $this->load($result['id']);
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
        catch (MySQLQueryException $ex) {
            error_log($ex->getMessage());
            return null;
        }
        if (!password_verify($this->_id.$password, $this->_password)) {
            return null;
        }
        else {
            return $this;
        }
    }

    /**
     * Loads account information from the database
     *
     * @param int $id User ID
     *
     * @return $this UserModel
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    public function load($id)
    {
        if (!($stmt = $this->db->prepare(
            "SELECT id, username, pwd
            FROM user
            WHERE id = ?;"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in UserModel::load');
        }
        if (!($stmt->bind_param('i', $id))) {
            throw new MySQLIStatementException('Error in bind_param() in UserModel::load');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in UserModel::load');
        }
        if (!($result = $stmt->get_result())) {
            throw new MySQLIStatementException('Error in get_result() in UserModel::load');
        }
        if ($result->num_rows == 0) {
            throw new MySQLQueryException("No account found with id '$id' in UserModel::load");
        }
        $result = $result->fetch_assoc();
        return $this->setID($result['id'])
            ->setUsername($result['username'])
            ->setPassword($result['pwd']);
    }

    /**
     * Saves account information to the database
     *
     * Should only be called after account model object's username and password has been set.

     * @return $this UserModel
     * @throws MySQLQueryException
     */
    public function save()
    {
        $username = mysqli_real_escape_string($this->db, $this->_username);
        if (!$result = $this->db->query(
            "INSERT INTO user
            VALUES (
                NULL,
                '$username',
                'temp',
                0.00
            );"
        )) {
            throw new MySQLQueryException('Error from "INSERT INTO user_account" in UserModel::save');
        }
        $this->_id = $this->db->insert_id;
        $password = mysqli_real_escape_string($this->db, $this->_password);
        if (!$result = $this->db->query(
            "UPDATE user_account
            SET pwd = '".password_hash($this->_id.$password, PASSWORD_DEFAULT)."'
            WHERE id = $this->_id"
        )) {
            throw new MySQLQueryException('Error from "UPDATE user_account SET" pwd in UserModel::save');
        }
        try {
            return $this->load($this->_id);
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }

    /**
     * Deletes account from the database

     * @return $this UserModel
     * @throws MySQLQueryException
     */
    public function delete()
    {
        if ($this->_username != 'admin') {
            $id = mysqli_real_escape_string($this->db, $this->_id);
            if (!$result = $this->db->query(
                "DELETE FROM user
                WHERE id = $id;"
            )) {
                throw new MySQLQueryException('Error from DELETE in UserModel::delete');
            }
        }
        else {
            throw new MySQLQueryException('Cannot delete admin account');
        }

        return $this;
    }

    /**
     * Update balance value for account.
     *
     * @throws MySQLQueryException
     */
    private function updateBalance() {
        $id = mysqli_real_escape_string($this->db, $this->_id);
        $balance = mysqli_real_escape_string($this->db, $this->_balance);
        if (!$result = $this->db->query(
            "UPDATE user
            SET balance = $balance
            WHERE id = $id;"
        )) {
            throw new MySQLQueryException('Error from UPDATE in UserModel::addToBalance');
        }
    }

    /**
     * Add amount to account balance.
     *
     * @param $amount
     * @throws MySQLQueryException
     */
    public function addToBalance($amount) {
        $this->_balance += $amount;
        try {
            $this->updateBalance();
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }

    /**
     * Subtract amount from account balance.
     *
     * @param $amount
     * @throws MySQLQueryException
     */
    public function subtractFromBalance($amount) {
        $this->_balance -= $amount;
        try {
            $this->updateBalance();
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }
}
