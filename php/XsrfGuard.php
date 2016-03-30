<?php

/**
 * Created by IntelliJ IDEA.
 * User: Marc
 * Date: 28.01.2016
 * Time: 23:44
 */
class XsrfGuard {

    private static $SESSION_KEY = "XSRF_SESSION_TOKEN";
    public static $XSRF_TOKEN = "xsrf";

    public function destroy() {
        $this->storeInSession("");
    }

    public function extractTokenFromRequest() : string {
        if ($_SERVER["REQUEST_METHOD"] == "GET" && array_key_exists(XsrfGuard::$XSRF_TOKEN, $_GET)) {
            return $_GET[XsrfGuard::$XSRF_TOKEN];
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && array_key_exists(XsrfGuard::$XSRF_TOKEN, $_POST)) {
            return $_POST[XsrfGuard::$XSRF_TOKEN];
        }

        return "";
    }

    public function getToken() : string {
        return $this->getFromSession();
    }

    public function generateXsrfToken() : string {
        if (function_exists("hash_algos") and in_array("sha512", hash_algos())) {
            $token = hash("sha512", mt_rand(0, mt_getrandmax()));
        } else {
            $token = ' ';
            for ($i = 0; $i < 128; ++$i) {
                $r = mt_rand(0, 35);
                if ($r < 26) {
                    $c = chr(ord('a') + $r);
                } else {
                    $c = chr(ord('0') + $r - 26);
                }
                $token .= $c;
            }
        }
        $this->storeInSession($token);

        return $token;
    }

    public function validateXsrfToken($token) : bool {
        $ref = $this->getFromSession();
        if ($ref === "") {
            return false;
        } elseif ($ref === $token) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    private function storeInSession(string $value) {
        if (isset($_SESSION)) {
            $_SESSION[XsrfGuard::$SESSION_KEY] = $value;
        }
    }

    private function getFromSession() : string {
        if (isset($_SESSION[XsrfGuard::$SESSION_KEY])) {
            return $_SESSION[XsrfGuard::$SESSION_KEY];
        }

        return "Not_found";
    }

}