<?php
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\User;



class UserController extends Controller { 


  public static function register(array $params, array $data): void {                             /*LoginButton-Push -> index.php -> controller -> model -> kahuna.php?*/

                                      $email = $data['email'];
                                      $password = $data['password'];
                                      $user = new User(email: $email, password: $password);
                                      $user = User::save($user);                              
                                      self::sendResponse($data = $user, $code = 201);
                                    }      /*sendResponse coming from: extend~Controller*/








                                              
}/*class~end*/