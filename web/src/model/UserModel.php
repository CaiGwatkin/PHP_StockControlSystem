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
     * @var string User's name.
     */
    private $_name;
    
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
     * @return string Username
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param string $_username Username
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
     * @return string User's name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $_name User's name
     *
     * @return UserModel $this
     */
    public function setName(string $_name)
    {
        $this->_name = $_name;

        return $this;
    }
    
    /**
     * Checks that login details are valid.
     *
     * @param string $username The username for the user to be logged in.
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
            throw new MySQLQueryException("No user found with username '$username' in UserModel::load");
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
     * Loads user information from the database
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
            "SELECT id, username, pwd, name
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
            throw new MySQLQueryException("No user found with id '$id' in UserModel::load");
        }
        $result = $result->fetch_assoc();
        return $this->setID($result['id'])
            ->setUsername($result['username'])
            ->setPassword($result['pwd'])
            ->setName($result['name']);
    }

    /**
     * Saves user information to the database
     *
     * Should only be called after user model object's username, password, and name has been set.

     * @return $this UserModel
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    public function save()
    {
        try {
            return $this->insert()
                ->updatePassword()
                ->load($this->_id);
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
        catch (MySQLQueryException $ex) {
            throw $ex;
        }
    }

    /**
     * Insert new user into database.
     *
     * @return $this UserModel
     * @throws MySQLIStatementException
     */
    private function insert()
    {
        if (!($stmt = $this->db->prepare(
            "INSERT INTO user
            VALUES (
                NULL,
                ?,
                ?,
                ?
            );"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in UserModel::insert');
        }
        if (!$stmt->bind_param('sss', $this->_username, $this->_password, $this->_name)) {
            throw new MySQLIStatementException('Error in bind_param() in UserModel::insert');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in UserModel::insert');
        }
        return $this;
    }

    /**
     * Generates and sets new password for user.
     *
     * @return $this UserModel
     * @throws MySQLIStatementException
     * @throws MySQLQueryException
     */
    private function updatePassword()
    {
        if (!($stmt = $this->db->prepare(
            "UPDATE user
            SET pwd = ?
            WHERE id = ?"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in UserModel::updatePassword');
        }
        $this->_id = $this->db->insert_id;
        $password = password_hash($this->_id.$this->_password, PASSWORD_DEFAULT);
        if (!($stmt->bind_param('si', $password, $this->_id))) {
            throw new MySQLIStatementException('Error in bind_param() in UserModel::updatePassword');
        }
        if (!$stmt->execute()) {
            throw new MySQLQueryException('Error in execute() in UserModel::updatePassword');
        }
        return $this;
    }

    /**
     * Checks if username exists in database.
     *
     * @param string $username Username
     *
     * @return bool True if username exists in database.
     * @throws MySQLIStatementException
     */
    public function usernameExists($username)
    {
        if (!($stmt = $this->db->prepare(
            "SELECT id
            FROM user
            WHERE username = ?;"
        ))) {
            throw new MySQLIStatementException('Error in prepare() in UserModel::usernameExists');
        }
        if (!($stmt->bind_param('s', $username))) {
            throw new MySQLIStatementException('Error in bind_param() in UserModel::usernameExists');
        }
        if (!$stmt->execute()) {
            throw new MySQLIStatementException('Error in execute() in UserModel::usernameExists');
        }
        if (!($result = $stmt->get_result())) {
            throw new MySQLIStatementException('Error in get_result() in UserModel::usernameExists');
        }
        if ($result->num_rows != 0) {
            return true;
        }
        return false;
    }

//    /**
//     * Deletes user from the database
//
//     * @return $this UserModel
//     * @throws MySQLQueryException
//     */
//    public function delete()
//    {
//        if ($this->_username != 'admin') {
//            $id = mysqli_real_escape_string($this->db, $this->_id);
//            if (!$result = $this->db->query(
//                "DELETE FROM user
//                WHERE id = $id;"
//            )) {
//                throw new MySQLQueryException('Error from DELETE in UserModel::delete');
//            }
//        }
//        else {
//            throw new MySQLQueryException('Cannot delete admin user');
//        }
//
//        return $this;
//    }
}
