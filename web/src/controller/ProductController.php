<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\controller;

use cgwatkin\a3\exception\LoadTemplateException;
use cgwatkin\a3\exception\MySQLDatabaseException;
use cgwatkin\a3\exception\MySQLIStatementException;
use cgwatkin\a3\exception\MySQLQueryException;
use cgwatkin\a3\model\ProductCollectionModel;
use cgwatkin\a3\view\View;

/**
 * Class ProductController
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class ProductController extends Controller
{
    /**
     * Product Search action
     *
     * Creates search view.
     */
    public function searchAction()
    {
        if ($this->userIsLoggedIn()) {
            try {
                if (isset($_GET['q'])) {
                    $orderBy = $_GET['orderBy']??'sku';
                    $sort = $_GET['sort']??'ASC';
                    $productCollection = new ProductCollectionModel($_GET['q'], 'name', $orderBy, $sort);
                    $products = $productCollection->getObjects();
                    echo (new View('productSearch'))
                        ->addData('products', $products)
                        ->render();
                }
                else {
                    echo (new View('productSearch'))->render();
                }
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
        else {
            $this->redirectAction('/login');
        }
    }

//    /**
//     * Verify Registration Form action
//     *
//     * Handles post from AJAX on user registration page.
//     */
//    public function verifyRegistrationFormAction() {
//        try {
//            if (isset($_POST['username'])) {
//                echo $this->usernameExists($_POST['username']) ? 'duplicate' : 'unique';
//            }
//        }
//        catch (MySQLDatabaseException $ex) {
//            error_log(self::$INTERNAL_SERVER_ERROR_MESSAGE.': '.$ex->getMessage());
//            echo null;
//        }
//        catch (MySQLIStatementException $ex) {
//            error_log(self::$INTERNAL_SERVER_ERROR_MESSAGE.': '.$ex->getMessage());
//            echo null;
//        }
//    }

    /**
     * Update Search Results action
     *
     * Handles GET from AJAX on search page.
     */
    public function updateSearchResults()
    {
        try {
            // echo json of products
//            header('Content-Type: application/json');
//            if (isset($_GET['needle'])) {
//                $productCollection = new ProductCollectionModel($_GET['needle'], 'name', $_GET['orderBy'],
//                    $_GET['sort']);
//                $products = $productCollection->getObjects();
//                $view = new View('search');
//                echo $view->addData('products', $products)
//                    ->render();
//            }
//            else {
//                echo null;
//            }
        }
        catch (MySQLQueryException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'MySQL error '.$ex->getMessage());
            return;
        }
        catch (LoadTemplateException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
    }
}
