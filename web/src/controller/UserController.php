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
use cgwatkin\a3\model\UserCollectionModel;
use cgwatkin\a3\model\Model;
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
            if (isset($_POST['login'])) {
                $username = $_POST['username'];
                $user = (new UserModel())
                    ->checkLogin($username, $_POST['password']);
                if (!$user) {
                    $view = new View('userLogin');
                    echo $view->addData('username', $username)
                        ->render();
                    return;
                }
                $username = $user->getUsername();
                session_start();
                $_SESSION['userID'] = $user->getID();
                $_SESSION['username'] = $username;
                $this->redirectAction('/welcome');
            } else if ($this->userIsLoggedIn()) {
                $this->redirectAction('/welcome');
            } else {
                $view = new View('userLogin');
                echo $view->render();
                return;
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
        try {
            $view = new View('userLogout');
            echo $view->render();
        }
        catch (LoadTemplateException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
        }
    }
    
    /**
     * User List action
     *
     * Lists accounts in system if user is admin.
     */
    public function listAction()
    {
        if ($this->userIsAdmin()) {
            $page = $_GET['page']??1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            try {
                $accountCollection = new UserCollectionModel($limit, $offset);
                $accounts = $accountCollection->getObjects();
                $view = new View('accountList');
                echo $view->addData('accounts', $accounts)
                    ->addData('numUsers', $accountCollection->getNum())
                    ->addData('page', $page)
                    ->render();
            }
            catch (MySQLQueryException $ex) {
                $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'MySQL error');
                return;
            }
            catch (LoadTemplateException $ex) {
                $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
                return;
            }
        }
        else {
            $this->redirectAction('/accessDenied');
        }
    }

    /**
     * User Create action
     *
     * If user is admin and request is not POST, display input for new account data.
     * If user is admin and request is POST, try to create account and display new account.
     */
    public function createAction() 
    {
        if ($this->userIsAdmin()) {
            if (isset($_POST['create'])) {
                $username = $_POST['username'];
                try {
                    $account = new UserModel();
                    $account->setUsername($username)
                        ->setPassword($_POST['password'])
                        ->save();
                    if (!$account) {
                        $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE,
                            'User creation failed. Did you enter a username?');
                        return;
                    }
                    $view = new View('accountCreate');
                    echo $view->addData('account', $account)
                        ->render();
                }
                catch (MySQLQueryException $ex) {
                    $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'User name "'.$username.'" already exists.');
                    return;
                }
                catch (LoadTemplateException $ex) {
                    $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
                    return;
                }
            }
            else {
                try {
                    $view = new View('accountCreate');
                }
                catch (LoadTemplateException $ex) {
                    $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
                    return;
                }
                echo $view->render();
            }
        }
        else {
            $this->redirectAction('/accessDenied');
        }
    }

    /**
     * User Delete action
     *
     * @param int $id User id to be deleted
     */
    public function deleteAction($id)
    {
        if ($this->userIsAdmin()) {
            try {
                $account = (new UserModel())->load($id);
                if (!$account) {
                    $view = new View('accountDeleted');
                    echo $view->addData('accountId', $id)
                        ->render();
                }
                else {
                    $account->delete();
                    $view = new View('accountDeleted');
                    echo $view->addData('accountExists', true)
                        ->addData('accountId', $id)
                        ->render();
                }
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
        else {
            $this->redirectAction('/accessDenied');
        }
    }
}
