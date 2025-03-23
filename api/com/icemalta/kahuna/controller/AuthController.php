<?php

/*Authentication&Listing Controllers
  eloszor a 'listings_view legyen. ez autentikus view, tehat meg kell oldani, hogy az user autentizalva legyen.

  The authentication model is present in model/User.php (for logging in and logging out)
  we use model/AccessToken.php, to generate access tokens*/

  namespace com\icemalta\kahuna\controller;
  use com\icemalta\kahuna\model\{accessToken, User};

  class AuthController extends Controller {

                      public static function login(User $user): void { //ez a kod csupan egyeztetest vegez, es tokent ad, vagy nem ad.
                                                      $user = User::authenticate($user);

                                                      if ($user) { 
                                                            $token = new AccessToken(userId: $user->getId()); 
                                                            $token = AccessToken::save($token); 
                                                            self::sendResponse(data: ['user' => $user->getId(), 'token' => $token->getToken()]); 
                                                      } else { 
                                                        self::sendResponse(code: 401, error: 'Login failed.'); 
                                                              }
                      }


        public static function logout(array $data): void { //forToken not for user
                                        if (self::checkToken($data)) { 
                                                            $userId = $data['user']; 
                                                            $token = new AccessToken(userId: $userId); 
                                                            $token = AccessToken::delete($token); 
                                                            self::sendResponse(data: ['message' => 'Logged out']); 
                                        }   else { 
                                            self::sendResponse(code: 403, error: 'Missing Token.'); 
                                                }
        }



        public static function verifyToken(array $params, array $data): void {
                                        if (self::checkToken($data)) { 
                                                  self::sendResponse(data: ['valid' => true, 'token' => $data['api_token']]); 
                                        }   else { 
                                              self::sendResponse(data: ['valid' => false, 'token' => $data['api_token']]); 
                                                }   
        }


        public static function connectionTest(array $params, array $data): void {
                                            self::sendResponse(data: 'Welcome Api!');
        }




  }/*class~end*/