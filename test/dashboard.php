<?php
require 'config.php';
require dirname(__DIR__).'/vendor/autoload.php';
use carry0987\RememberMe\RememberMe as RememberMe;
use carry0987\RememberMe\DBController as DBController;

$get_path = dirname($_SERVER['PHP_SELF']);
$rememberMe = new RememberMe($get_path);
$isLoggedIn = false;

session_start();

//Check if loggedin session and redirect if session exists
if (!empty($_SESSION['username'])) {
    $isLoggedIn = true;
}
//Check if loggedin session exists
elseif (!empty($_COOKIE['user_login']) && !empty($_COOKIE['random_pw']) && !empty($_COOKIE['random_selector'])) {
    $db = new DBController();
    $db->connectDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $rememberMe->getDB($db);
    $checkRemember = $rememberMe->checkUserInfo($_COOKIE['user_login'], $_COOKIE['random_selector'], $_COOKIE['random_pw']);
    if ($checkRemember !== false) {
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
