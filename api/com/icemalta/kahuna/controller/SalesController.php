<?php
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\Sale;

class SalesController extends Controller {


                        //save sale
                        public static function save(int $userId, int $productId): void {
                            $sale = new Sale( $userId, $productId);
                            $sale = Sale::save($sale); 
                            self::sendResponse(data: $sale); 
                        }

                    



                        // get list of sales
                        public static function getSales() {}


                        // get warranty info
                        public static function getWarranty() {}



                        // delete sale
                        public static function delete(int $id) {
                            $sale = Sale::delete($id);
                            self::sendResponse(data: $id);
                    }

}