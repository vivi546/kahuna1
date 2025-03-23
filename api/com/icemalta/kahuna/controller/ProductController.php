<?php
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\Product;

class ProductController extends Controller {



// get product list
public static function load(): void {
                                    $product = Product::load(); 
                                    self::sendResponse(data: $product); 
                            }



// save product
public static function save(string $serial, string $name, int $warranty): void {
                                $product = new Product($serial, $name, $warranty);
                                $product = Product::save($product); 
                                self::sendResponse(data: $product); 
                            }


// delete product
public static function delete(int $id) {
        $product = Product::delete($id);
        self::sendResponse(data: $product);
}

}