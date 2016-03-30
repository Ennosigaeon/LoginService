<?php
require_once "php/LoginController.php";


function validate($name, $pw) : bool {
    return $name == "foo" && $pw == "bar";
}

$loginController = LoginController::getInstance();
$loginController->setValidationFunction("validate");
if ($loginController->processRequest()) {
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LoginService</title>

    <link rel="stylesheet" href="lib/bootstrap.min.css">
    <link rel="stylesheet" href="lib/bootstrap-theme.min.css">

    <script src="lib/jquery-2.1.3.js"></script>
    <script src="lib/jquery.cookie.js"></script>
    <script src="lib/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $.loginService({
                controller: 'index.php',
                loginTarget: 'index.php',
                logoutTarget: 'index.php'
            });
        });
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <?php
        if ($loginController->isLoggedIn()) {
            print '<button class="btn btn-default logoutButton">Logout</button>';
        }
        else {
            print '<button class="btn btn-default loginButton">Login</button>';
        } ?>
    </div>
</div>

<?php include "js/login.html" ?>

</body>
</html>