<?php
namespace com\icemalta\kahuna\controller;
use com\icemalta\kahuna\model\User;

class UserController extends Controller {

    public static function register(array $data): void {
        $email = $data['email'];
        $password = $data['password'];
        $user = new User(email: $email, password: $password);
        $user = User::save($user);
        self::sendResponse($data = $user, $code = 201);
    }

    public static function getUserByEmail(string $email, array $data): void {
        if (self::checkToken($data)) {
            $user = User::getUser($email);
            if($user){
                self::sendResponse($user);
            } else {
                self::sendResponse(null, 404, 'User not found');
            }
        }
    }

    public static function delete(string $email): void {
       
            $delete = User::delete($email);
          
                self::sendResponse(null, 200, 'User deleted successfully');
    }

    // You can add an authenticate function here if needed.
    public static function authenticate(array $data): void {
        $email = $data['email'];
        $password = $data['password'];
        $user = new User(email: $email, password: $password);
        $authenticatedUser = User::authenticate($user);
        if ($authenticatedUser) {
            self::sendResponse($authenticatedUser);
        } else {
            self::sendResponse(null, 401, 'Authentication failed');
        }
    }
}
