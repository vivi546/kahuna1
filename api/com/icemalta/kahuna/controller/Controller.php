<?php
/*✅superclass*/
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\AccessToken;  /*ez meg nincs ott*/

class Controller {


    /*SendResponse - OK$data vagy error*/   
    public static function sendResponse(mixed $data = null,
                                        int $code = 200,
                                        mixed $error = null): void {

                                                if (!is_null($data)) {
                                                    $response['data'] = $data;
                                                }

                                                if (!is_null($error)) {
                                                    $response['error'] = [
                                                        'message' => $error,
                                                        'code' => $code
                                                        ];
                                                }
                                        http_response_code($code);
                                        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }


/*CheckToken*/
    public static function checkToken(array $requestData): bool {

                                        if (!isset($requestData['api_user']) || !isset($requestData['api_token'])) {
                                            return false;
                                        }
                                        $token = new AccessToken($requestData['api_user'], $requestData['api_token']);
                                        return AccessToken::verify($token);
    }
}