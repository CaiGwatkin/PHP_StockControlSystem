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
                echo (new View('productSearch'))->addData('pageName', 'Search')
                    ->addData('scripts', array('productSearchHandler'))
                    ->render();
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
        else {
            $this->redirectAction('/login');
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
                    array_push($objectVarArray, $product->exposeVars());
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
