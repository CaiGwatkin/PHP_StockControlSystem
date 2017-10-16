<?php
/*
 * Gwatkin, 15146508
 */

/**
 * 159.339 Internet Programming 2017.2
 * Student ID: 15146508
 * Assignment: 3   Date: 30/09/17
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

require __DIR__ . '/vendor/autoload.php';

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

/**
 * Paths to resources in server.
 */
define('APP_ROOT', __DIR__);
define('COMPONENT', APP_ROOT.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'template'
    .DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR);
define('BUTTON_COMPONENT', COMPONENT.'button'.DIRECTORY_SEPARATOR);
define('HEADER_COMPONENT', COMPONENT.'header'.DIRECTORY_SEPARATOR);
define('FOOTER_COMPONENT', COMPONENT.'footer'.DIRECTORY_SEPARATOR);
define('MENU_COMPONENT', COMPONENT.'menu'.DIRECTORY_SEPARATOR);
define('IMAGE_COMPONENT', COMPONENT.'image'.DIRECTORY_SEPARATOR);

/**
 * Database information.
 */
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'cgwatkin_a3');

/***********************************************************************************************************************
 * MANAGE ROUTING
 */
$collection = new RouteCollection();

$collection->attachRoute(
    new Route(
        '/', array(
            '_controller' => 'cgwatkin\a3\controller\Controller::welcomeAction',
            'methods' => 'GET',
            'name' => 'welcome'
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
        '/logout', array(
            '_controller' => 'cgwatkin\a3\controller\UserController::logoutAction',
            'methods' => 'GET',
            'name' => 'userLogout'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/register', array(
            '_controller' => 'cgwatkin\a3\controller\UserController::registerAction',
            'methods' => array('GET', 'POST'),
            'name' => 'userRegister'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/js/verifyRegistrationForm', array(
            '_controller' => 'cgwatkin\a3\controller\UserController::verifyRegistrationFormAction',
            'methods' => 'POST',
            'name' => 'userVerifyRegistrationForm'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/search', array(
            '_controller' => 'cgwatkin\a3\controller\ProductController::searchAction',
            'methods' => 'GET',
            'name' => 'productSearch'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/js/updateSearchResults', array(
            '_controller' => 'cgwatkin\a3\controller\ProductController::updateSearchResults',
            'methods' => 'GET',
            'name' => 'productUpdateSearchResults'
        )
    )
);

/***********************************************************************************************************************
 * CREATE ROUTE
 */
$router = new Router($collection);
$router->setBasePath('/');
if ($router->matchCurrentRequest()) {
    return true;
}

/***********************************************************************************************************************
 * MANAGE RESOURCES
 */
$info = parse_url($_SERVER['REQUEST_URI']);
if (preg_match('/\.(?:png|jpg|jpeg|css|js)$/', "$info[path]")
    && file_exists("./$info[path]")
) {
    return false;
}
else {
    header("HTTP/1.0 404 Not Found");
    return true;
}
