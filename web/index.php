<?php
/**
 * 159.339 Internet Programming 2017.2
 * Student ID: 15146508
 * Assignment: 2   Date: 30/09/17
 * System: PHP 7.1
 * Code guidelines: PSR-1, PSR-2
 *
 * FRONT CONTROLLER - Responsible for URL routing and User Authentication
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 **/
date_default_timezone_set('Pacific/Auckland');

require __DIR__ . '/vendor/autoload.php';

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

define('APP_ROOT', __DIR__);
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'cgwatkin_a3');

setlocale(LC_ALL, 'en-NZ'); // Ensure consistent currency format
// dirty workaround (should be set by user and checked later)

$collection = new RouteCollection();

$collection->attachRoute(
    new Route(
        '/', array(
            '_controller' => 'cgwatkin\a3\controller\Controller::redirectAction',
            'methods' => 'GET',
            'name' => 'indexRedirect',
            'parameters' => array('url' => '/login')
        )
    )
);

$collection->attachRoute(
    new Route(
        '/login', array(
            '_controller' => 'cgwatkin\a3\controller\UserController::loginAction',
            'methods' => array('GET', 'POST'),
            'name' => 'userLogin'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/logout', array(
            '_controller' => 'cgwatkin\a3\controller\AccountController::logoutAction',
            'methods' => 'GET',
            'name' => 'accountLogout'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/list', array(
            '_controller' => 'cgwatkin\a3\controller\AccountController::listAction',
            'methods' => 'GET',
            'name' => 'accountList'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/accessDenied', array(
            '_controller' => 'cgwatkin\a3\controller\AccountController::accessDeniedAction',
            'methods' => 'GET',
            'name' => 'accountAccessDenied'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/create', array(
            '_controller' => 'cgwatkin\a3\controller\AccountController::createAction',
            'methods' => array('GET', 'POST'),
            'name' => 'accountCreate'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/delete/:id', array(
            '_controller' => 'cgwatkin\a3\controller\AccountController::deleteAction',
            'methods' => 'GET',
            'name' => 'accountDelete'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/transfer/list', array(
            '_controller' => 'cgwatkin\a3\controller\TransferController::listAction',
            'methods' => 'GET',
            'name' => 'transferList'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/transfer/make', array(
            '_controller' => 'cgwatkin\a3\controller\TransferController::makeAction',
            'methods' => array('GET', 'POST'),
            'name' => 'transferMake'
        )
    )
);

$router = new Router($collection);
$router->setBasePath('/');

$route = $router->matchCurrentRequest();

// If route was dispatched successfully - return
if ($route) {
    // true indicates to webserver that the route was successfully served
    return true;
}

// Otherwise check if the request is for a static resource
$info = parse_url($_SERVER['REQUEST_URI']);
// check if its allowed static resource type and that the file exists
if (preg_match('/\.(?:png|jpg|jpeg|css|js)$/', "$info[path]")
    && file_exists("./$info[path]")
) {
    // false indicates to web server that the route is for a static file - fetch it and return to client
    return false;
} else {
    header("HTTP/1.0 404 Not Found");
    // Custom error page
    // require 'static/html/404.html';
    return true;
}
