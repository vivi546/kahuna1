<?php
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\Sale;

class SalesController extends Controller {


                        //save sale
                        public static function save(int $userId, int $productId, array $data): void {
                            if (self::checkToken($data)) { 
                                                $sale = new Sale( $userId, $productId);
                                                $sale = Sale::save($sale); 
                                                self::sendResponse(data: $sale);
                            } else { 
                                self::sendResponse(code: 403, error: 'Missing, invalid or expired token.'); 
                                        }
                        }

                    



                        // get list of sales = 
                        public static function getSales(): void {
                            $sales = Sale::load();
                    
                            self::sendResponse(data: $sales);
                        }


                        
                        


                        // get warranty info
                        public static function getWarranty(string $userId, string $serial) {
                            if (!$userId || !$serial) {
                                sendResponse(null, 400, 'Missing parameters');
                                return;
                            }
                        
                            $sales = Sale::getWarrantyInfo($userId, $serial);
                        
                            if (!$sales) {
                                sendResponse(null, 404, 'No matching records found');
                                return;
                            }
                        
                            sendResponse($sales, 200);
                        }



                        // delete sale
                        public static function delete(int $id) {
                            if (!$id) {
                                sendResponse(null, 400, 'Missing sale ID');
                                return;
                            }
                    
                            $deleted = Sale::delete($id);
                            if ($deleted) {
                                sendResponse('Sale deleted successfully', 200, );
                            } else {
                                sendResponse(null, 404, 'Sale not found');
                            };
                    }

}