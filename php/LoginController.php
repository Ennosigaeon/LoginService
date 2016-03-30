<?php

/**
 * Created by IntelliJ IDEA.
 * User: Marc
 * Date: 28.01.2016
 * Time: 23:43
 */

include_once $_SERVER["DOCUMENT_ROOT"] . "/php/XsrfGuard.php";


class LoginController {

    public static $LOGIN_NAME = "username";
    private static $instance = null;

    private $validationFunc;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new LoginController();
        }

        return self::$instance;
    }

    public function isLoggedIn() {
        session_start();

        return array_key_exists(LoginController::$LOGIN_NAME, $_SESSION);
    }

    public function logIn($name, $pw) {
        if ($this->validCredentials($name, $pw)) {
            session_start();
            $_SESSION[LoginController::$LOGIN_NAME] = $name;

            $xsrfGuard = new XsrfGuard();
            $xsrfGuard->generateXsrfToken();

            return true;
        }

        return false;
    }

    public function logOut() {
        if ($this->isLoggedIn()) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
            session_destroy();
        }
    }

    public function setValidationFunction($validationFunc) {
        $this->validationFunc = $validationFunc;
    }

    /**
     * Convenience function for automatic request procession
     *
     * @return bool Returns whether this request was handled or not
     */
    public function processRequest() {
        //request should not be handled by LoginController
        if (!array_key_exists('loginController', $_GET) && !array_key_exists('loginController', $_POST)) {
            return false;
        }

        if ($_SERVER["REQUEST_METHOD"] == 'GET' && isset($_GET['cookie'])) {
            if ($this->isLoggedIn()) {
                http_response_code(200);
            }
            else {
                http_response_code(403);
            }
        }
        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            if (array_key_exists("logout", $_POST)) {
                $this->logOut();
            }
            else {
                $name = $_POST["name"];
                $pw = $_POST["pw"];
                if ($this->logIn($name, $pw)) {
                    http_response_code(200);
                }
                else {
                    http_response_code(403);
                }
            }
        }

        return true;
    }

    private function validCredentials($name, $pw) {
        return call_user_func($this->validationFunc, $name, $pw);
    }

}