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
        try {
            if ($this->userIsLoggedIn()) {
                echo (new View(PRODUCT_SEARCH_TEMPLATE))
                    ->addData('pageName', PRODUCT_SEARCH_PAGE_NAME)
                    ->addData('scripts', array(PRODUCT_SEARCH_RESULTS_SCRIPT))
                    ->render();
            }
            else {
                $this->redirectAction(USER_LOGIN_PAGE);
            }
        }
        catch (MySQLDatabaseException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, $ex->getMessage());
            return;
        }
        catch (MySQLIStatementException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'Invalid parameters in URL');
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
     * Update Search Results action
     *
     * Handles GET from AJAX on search page. Sends back JSON encoded collection of products.
     */
    public function updateSearchResults()
    {
        try {
            if (isset($_GET['q'])) {
                error_log($_GET['q']);
                header('Content-Type: application/json');
                $orderBy = $_GET['orderBy']??'sku';
                $sort = $_GET['sort']??'ASC';
                $productCollection = new ProductCollectionModel($_GET['q'], 'name', $orderBy, $sort);
                $products = $productCollection->getObjects();
                $objectVarArray = array();
                foreach($products as $product) {
                    array_push($objectVarArray, $product->exposeVariables());
                }
                echo json_encode($objectVarArray);
            }
            else {
                echo null;
            }
        }
        catch (MySQLQueryException $ex) {
            $this->errorAction(self::$INTERNAL_SERVER_ERROR_MESSAGE, 'MySQL error '.$ex->getMessage());
            return;
        }
    }
}
