<?php
/*util*/
require 'com/icemalta/kahuna/util/ApiUtil.php';
/*/controller*/
require 'com/icemalta/kahuna/controller/ProductController.php';
/*/model*/
require 'com/icemalta/kahuna/model/Sales.php';   
require 'com/icemalta/kahuna/model/Product.php';
require 'com/icemalta/kahuna/model/User.php';   


/*model*/
use com\icemalta\kahuna\model\Product;
use com\icemalta\kahuna\model\Sale;
use com\icemalta\kahuna\model\User;
/*controller*/
use com\icemalta\kahuna\controller\ProductController;
/*util*/
use com\icemalta\kahuna\util\ApiUtil;



cors();

$endPoints = [];
$requestData = [];
header("Content-Type: application/json; charset=UTF-8");

/* BASE URI */
$BASE_URI = '/kahuna/api/';

function sendResponse(mixed $data = null, int $code = 200, mixed $error = null): void
{
    if (!is_null($data)) {
        $response['data'] = $data;
    }
    if (!is_null($error)) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    http_response_code($code);
}

/* Get Request Data */
$requestMethod = $_SERVER['REQUEST_METHOD'];
switch ($requestMethod) {
    case 'GET':
        $requestData = $_GET;
        break;
    case 'POST':
        $requestData = $_POST;
        break;
    case 'PATCH':
        parse_str(file_get_contents('php://input'), $requestData);
        ApiUtil::parse_raw_http_request($requestData);
        $requestData = is_array($requestData) ? $requestData : [];
        break;
    case 'DELETE':
        break;
    default:
        sendResponse(null, 405, 'Method not allowed.');
}


/* Extract EndPoint */
$parsedURI = parse_url($_SERVER["REQUEST_URI"]);
$path = explode('/', str_replace($BASE_URI, "", $parsedURI["path"]));
$endPoint = $path[0];
$requestData['dataId'] = isset($path[1]) ? $path[1] : null;
if (empty($endPoint)) {
    $endPoint = "/";
}

/* Extract Token */
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["user"] = $_SERVER["HTTP_X_API_USER"];
}
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["token"] = $_SERVER["HTTP_X_API_KEY"];
}

/* EndPoint Handlers */
$endpoints["/"] = function (string $requestMethod, array $requestData): void {
    sendResponse('Welcome to Kahuna API!');
};

$endpoints["404"] = function (string $requestMethod, array $requestData): void {
    sendResponse(null, 404, "Endpoint " . $requestData["endPoint"] . " not found.");
};


// try { /*API végpontok meghívása -------------------------------------------------------------------------------------*/
//     if (isset($endpoints[$endPoint])) { //osszes endpoint. [$endPoint]->endpoints gyujtemenybol a megfelelot valasztjuk.
//                         $endpoints[$endPoint]($requestMethod, $requestData);
//     } else {
//       $endpoints["404"]($requestMethod, array("endPoint" => $endPoint));
//             }
// } catch (Exception $e) {
//          sendResponse(null, 500, $e->getMessage());
//   }   catch (Error $e) {
//             sendResponse(null, 500, $e->getMessage());
//       }



/*Product----------*/
$endpoints["product"] = function (string $requestMethod, array $requestData): void {
                    if($requestMethod === 'GET') {
                                $products = Product::load(); /*load in class_Product */
                                sendResponse($products);
                    }
                        elseif($requestMethod === 'POST') {
                                $serial = $requestData['serial'];
                                $name = $requestData['name'];
                                $warranty = $requestData['warranty'];

                                $product = new Product($serial, $name, $warranty);
                                $product = Product::save($product);
                                sendResponse($product, 201); 
                        }   elseif ($requestMethod === 'DELETE') {
                            $id = $requestData['id'];
                            sendResponse(null, 501, 'Deleting');

                        } else {
                            sendResponse(null, 405, 'Method Not allowed');
                                 }
};



/*user-------------*/
$endpoints["user"] = function (string $requestMethod, array $requestData): void {
                                if ($requestMethod === 'POST') {
                                                        $email = $requestData['email'];
                                                        $password = $requestData['password'];
                                                        $user = new User($email, $password);
                                                        $user = User::save($user);
                                                        sendResponse($user, 201);
                                } elseif($requestMethod === 'GET') {
                                    $email = $requestData['email'];
                                    $user = new User(email: $email);
                                    $user = User::getUser($email);
                                    sendResponse($user);
                                } else if ($requestMethod === 'PATCH') {
                                                        sendResponse(null, 501, 'Update not implemented yet');
                                    }   else if($requestMethod === 'DELETE') {
                                                        sendResponse(null, 501, 'Deleting' );
                                        }   else {
                                                sendResponse(null, 405, 'Method_not_Allowed');
                                            }
};


/*sale-----------*/
$endpoints["sale"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        $sales = Sale::load(); // Load all sales from the database
        sendResponse($sales);
    } elseif ($requestMethod === 'POST') {
        $userId = $requestData['userId'] ?? null;
        $productId = $requestData['productId'] ?? null;
        
        if (!$userId || !$productId) {
            sendResponse(null, 400, 'Missing userId or productId');
            return;
        }
        
        $sale = new Sale($userId, $productId);
        $sale = Sale::save($sale);
        sendResponse($sale, 201);
    } elseif ($requestMethod === 'DELETE') {
        $id = $requestData['id'] ?? null;
        
        if (!$id) {
            sendResponse(null, 400, 'Missing sale ID');
            return;
        }
        
        $deleted = Sale::delete($id);
        if ($deleted) {
            sendResponse(null, 200, 'Sale deleted successfully');
        } else {
            sendResponse(null, 404, 'Sale not found');
        }
    } else {
        sendResponse(null, 405, 'Method Not Allowed');
    }
};

/*warranty----------------*/
$endpoints['check-warranty'] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod !== 'GET') {
        sendResponse(null, 405, 'Method Not Allowed');
        return;
    }

    $userId = $requestData['userId'] ?? null;
    $serial = $requestData['serial'] ?? null;

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
};





/*GET view product list, or a product
  POST register product
  POST delete product
   */

/*user*/

/*POST register
    POST login - token
    POST logout/


/*tickets?
    POST create ticket ticket nyitasa ha van garancia
    */

    /*admin
    GET - admin get tickets
    POST - reply ticket
    POST - add product  */






/*--------------------------------------------------------------------------------------------------------------------*/

function cors()
{
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

try {
    if (isset($endpoints[$endPoint])) {
        $endpoints[$endPoint]($requestMethod, $requestData);
    } else {
        $endpoints["404"]($requestMethod, array("endPoint" => $endPoint));
    }
} catch (Exception $e) {
    sendResponse(null, 500, $e->getMessage());
} catch (Error $e) {
    sendResponse(null, 500, $e->getMessage());
}