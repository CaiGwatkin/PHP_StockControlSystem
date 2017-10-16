<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\controller;

use cgwatkin\a3\exception\LoadTemplateException;
use cgwatkin\a3\exception\MySQLDatabaseException;
use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;
use cgwatkin\a3\model\UserModel;
use cgwatkin\a3\view\View;

/**
 * Class UserController
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class UserController extends Controller
{
    /**
     * User Login action
     */
    public function loginAction()
    {
        try {
            $view = (new View('userLogin'))->addData('pageName', 'Login');
            if (isset($_POST['login'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $user = (new UserModel())->checkLogin($username, $password);
                if (!$user) {
                    echo $view->addData('invalid', true)
                        ->addData('username', $username)
                        ->addData('password', $password)
                        ->render();
                    return;
                }
                session_start();
                $_SESSION['name'] = $user->getName();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['userID'] = $user->getID();
                $this->redirectAction('/');
            }
            else if ($this->userIsLoggedIn()) {
                $this->redirectAction('/');
            }
            else {
                echo $view->render();
            }
        }
        catch (MySQLDatabaseException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (MySQLIStatementException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (MySQLQueryException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (LoadTemplateException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
    }
    
    /**
     * User Logout action
     *
     * Destroys the session, logging the user out.
     */
    public function logoutAction()
    {
        session_start();
        session_destroy();
        $this->redirectAction('/login');
    }

    /**
     * User Register action
     */
    public function registerAction()
    {
        try {
            if (!$this->userIsLoggedIn()) {
                $view = (new View('userRegister'))->addData('pageName', 'Register');
                if (isset($_POST['register'])) {
                    $name = $_POST['name'];
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $formError = $this->checkRegistrationForm($username, $password, $_POST['passwordRepeat']);
                    if ($formError) {
                        echo $view->addData('formError', $formError)
                            ->addData('name', $name)
                            ->addData('username', $username)
                            ->addData('password', $password)
                            ->addData('scripts', array('userRegisterFormHandler'))
                            ->render();
                        return;
                    }
                    $user = (new UserModel())->setName($name)
                        ->setUsername($username)
                        ->setPassword($password)
                        ->save();
                    if (!$user) {
                        $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'User registration failed');
                        return;
                    }
                    session_start();
                    $_SESSION['name'] = $user->getName();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['userID'] = $user->getID();
                    $this->redirectAction('/');
                } else {
                    echo $view->addData('scripts', array('userRegisterFormHandler'))
                        ->render();
                }
            }
            else {
                $this->redirectAction('/');
            }
        }
        catch (MySQLDatabaseException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (MySQLQueryException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (MySQLIStatementException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (LoadTemplateException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
    }

    /**
     * Verify Registration Form action
     *
     * Handles POST from AJAX on user registration page.
     */
    public function verifyRegistrationFormAction() {
        try {
            if (isset($_POST['username'])) {
                echo $this->usernameExists($_POST['username']) ? 'duplicate' : 'unique';
            }
        }
        catch (MySQLDatabaseException $ex) {
            error_log(self::$INTERNAL_SERVER_ERROR_MESSAGE.': '.$ex->getMessage());
            echo null;
        }
        catch (MySQLIStatementException $ex) {
            error_log(self::$INTERNAL_SERVER_ERROR_MESSAGE.': '.$ex->getMessage());
            echo null;
        }
    }

    /**
     * Checks that registration form data is as expected.
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $passwordRepeat Repeated password
     * @return null|string Description of registration form error, if one occurred.
     * @throws MySQLDatabaseException
     * @throws MySQLIStatementException
     */
    private function checkRegistrationForm($username, $password, $passwordRepeat)
    {
        try {
            if (!$this->usernameValid($username)) {
                return 'Invalid username: must contain alphanumeric characters only';
            }
            if ($this->usernameExists($username)) {
                return 'Invalid username: username already exists';
            }
            if (!$this->passwordValid($password)) {
                return 'Invalid password: password must be between 7 and 15 (exclusive) alphanumeric characters and '.
                    'contain at least one uppercase letter (no special characters allowed)';
            }
            if (!$this->passwordsMatch($password, $passwordRepeat)) {
                return 'Invalid password: passwords do not match';
            }
            return null;
        }
        catch (MySQLDatabaseException $ex) {
            throw $ex;
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
    }

    /**
     * Checks that username is valid.
     *
     * @param string $username Username
     * @return bool True if username contains only alphanumeric characters.
     */
    private function usernameValid($username)
    {
        return ctype_alnum($username);
    }

    /**
     * Checks whether username exists in database already.
     *
     * @param string $username Username
     * @return bool True if username already exists in database.
     * @throws MySQLDatabaseException
     * @throws MySQLIStatementException
     */
    private function usernameExists($username)
    {
        try {
            return (new UserModel())->usernameExists($username);
        }
        catch (MySQLDatabaseException $ex) {
            throw $ex;
        }
        catch (MySQLIStatementException $ex) {
            throw $ex;
        }
    }

    /**
     * Checks that password is valid.
     *
     * @param string $password Password
     * @return bool True if password is valid.
     */
    private function passwordValid($password)
    {
        $length = strlen($password);
        return $length > 7 && $length < 15 && preg_match('/[A-Z]/', $password) && ctype_alnum($password);
    }

    /**
     * Checks that password and repeated password match.
     *
     * @param string $password Password
     * @param string $passwordRepeat Repeated password
     * @return bool True if passwords match exactly.
     */
    private function passwordsMatch($password, $passwordRepeat)
    {
        return $password == $passwordRepeat;
    }
}
