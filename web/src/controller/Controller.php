<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\controller;

use cgwatkin\a3\exception\LoadTemplateException;
use cgwatkin\a3\view\View;

/**
 * Class Controller
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class Controller
{
    /**
     * @var string The message for internal server errors.
     */
    static $INTERNAL_SERVER_ERROR_MESSAGE = '500 Internal Server Error';

    /**
     * Redirect browser to new URL.
     *
     * @param string $url The new URL to be redirected to.
     * @param int $statusCode The HTTP status code for redirection. 303 by default.
     */
    public function redirectAction($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    /**
     * Welcome action
     *
     * Displays welcome page to user.
     */
    public function welcomeAction()
    {
        try {
            if ($this->userIsLoggedIn()) {
                echo (new View('welcome'))->addData('pageName', 'Welcome')
                    ->addData('name', $_SESSION['name'])
                    ->render();
            } else {
                $this->redirectAction('/login');
            }
        }
        catch (LoadTemplateException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
    }

    /**
     * Error action
     *
     * Creates an error view to display error message to user.
     *
     * @param string $error The error (code + type).
     * @param string $message The error message.
     */
    function errorAction(string $error, string $message)
    {
        try {
            error_log($error.': '.$message);
            echo (new View('error'))->addData('pageName', 'Error')
                ->addData('error', $error)
                ->addData('errorMessage', $message)
                ->render();
        }
        catch (LoadTemplateException $ex) {
            echo self::$INTERNAL_SERVER_ERROR_MESSAGE.': '.$ex->getMessage();
            return;
        }
    }

    /**
     * Checks if any user is logged in.
     *
     * @return bool Whether any user is logged in.
     */
    function userIsLoggedIn()
    {
        session_start();
        return isset($_SESSION['userID']);
    }
}
