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

    public static function getInstance(): LoginController {
        if (self::$instance == null) {
            self::$instance = new LoginController();
        }

        return self::$instance;
    }

    public function isLoggedIn() : bool {
        session_start();

        return array_key_exists(LoginController::$LOGIN_NAME, $_SESSION);
    }

    public function logIn(string $name, string $pw) : bool {
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
    public function processRequest() : bool {
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

    private function validCredentials(string $name, string $pw) {
        return call_user_func($this->validationFunc, $name, $pw);
    }

//    private function validCredentials(string $name, string $pw) : bool {
//        $con = AppConfig::getConnection();
//        $stat = $con->prepare("SELECT * FROM user WHERE username = :name");
//        $stat->execute(array(":name" => $name));
//
//        $row = $stat->fetch();
//        if (!$row) {
//            return false;
//        }
//
//        $hash = hash("sha256", $pw . $row["salt"]);
//
//        return hash_equals($row["pw"], $hash);
//    }

}