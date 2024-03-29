<?php
require dirname(__DIR__).'/vendor/autoload.php';
require 'config.php';
use carry0987\RememberMe\RememberMe as RememberMe;
// Just for example
use carry0987\RememberMe\Example\DBController as DBController;
use carry0987\RememberMe\Example\CookieHandler as CookieHandler;

$get_path = dirname($_SERVER['PHP_SELF']);
$db = new DBController;
$db->connectDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$cookieHandler = new CookieHandler;
$cookieHandler->setPath($get_path);
$rememberMe = new RememberMe($db, $cookieHandler);
$isLoggedIn = false;

session_start();

//Check if loggedin session and redirect if session exists
if (!empty($_SESSION['username'])) {
    $isLoggedIn = true;
}
//Check if loggedin session exists
elseif (!empty($_COOKIE['user_login']) && !empty($_COOKIE['random_pw']) && !empty($_COOKIE['random_selector'])) {
    $checkRemember = $rememberMe->verifyToken($_COOKIE['user_login'], $_COOKIE['random_selector'], $_COOKIE['random_pw']);
    if (!empty($checkRemember)) {
        $_SESSION['username'] = $checkRemember['username'];
        $isLoggedIn = true;
    }
}

if (!$isLoggedIn) {
    header('Location: ./');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DashBoard</title>
    <style type="text/css">
    .member-dashboard {
        padding: 40px;
        background: #D2EDD5;
        color: #555;
        border-radius: 4px;
        display: inline-block;
    }

    .member-dashboard a {
        color: #09F;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="member-dashboard">
        <span>You have Successfully logged in ! <a href="logout.php">Logout</a></span>
    </div>
</body>
</html>
