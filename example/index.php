<?php
require 'config.php';
require dirname(__DIR__).'/vendor/autoload.php';
use carry0987\RememberMe\RememberMe as RememberMe;
use carry0987\RememberMe\DBController as DBController;

$get_path = dirname($_SERVER['PHP_SELF']);

$db = new DBController;
$db->connectDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Inject DBController instance to RememberMe
$rememberMe = new RememberMe($db, $get_path);

$isLoggedIn = false;
session_start();

// Check if logged in session and redirect if session exists
if (!empty($_SESSION['username'])) {
    $isLoggedIn = true;
} elseif (!empty($_COOKIE['user_login']) && !empty($_COOKIE['random_pw']) && !empty($_COOKIE['random_selector'])) {
    $checkRemember = $rememberMe->checkUserInfo($_COOKIE['user_login'], $_COOKIE['random_selector'], $_COOKIE['random_pw']);
    if ($checkRemember !== false) {
        $_SESSION['username'] = $checkRemember['username'];
        $isLoggedIn = true;
    }
}

if ($isLoggedIn === true) {
    header('Location: dashboard.php');
    exit();
}

if (!empty($_POST['login'])) {
    $isAuthenticated = false;
    $username = $_POST['member_name'];
    $password = $_POST['member_password'];
    $user = $db->getUserByName($username);
    $user = $user[0];
    if (password_verify($password, $user['password'])) {
        $isAuthenticated = true;
    }
    if ($isAuthenticated === true) {
        $_SESSION['username'] = $username;
        //Set remember me
        $cookie_expiration_time = time() + (30 * 24 * 60 * 60);
        $year_time = time() + (1 * 365 * 24 * 3600);
        //Set Auth Cookies if 'Remember Me' checked
        if (!empty($_POST['remember'])) {
            $rememberMe->setCookie('user_login', $user['uid'], $year_time);
            $random_password = $rememberMe->getToken(16);
            $rememberMe->setCookie('random_pw', $random_password, $cookie_expiration_time);
            $random_pw_hash = password_hash($random_password, PASSWORD_DEFAULT);
            $expiry_date = $cookie_expiration_time;
            $selector = (isset($_COOKIE['random_selector'])) ? $_COOKIE['random_selector'] : 0;
            //Mark existing token as expired
            $userToken = $db->getTokenByUserID($user['uid'], $selector);
            if ($userToken !== false) {
                $db->updateToken($user['uid'], $selector, $random_pw_hash);
            } else {
                $random_selector = $rememberMe->getToken(16);
                $rememberMe->setCookie('random_selector', $random_selector, $year_time);
                //Insert new token
                $db->insertToken($user['uid'], $random_selector, $random_pw_hash, $expiry_date);
            }
        } else {
            $rememberMe->clearAuthCookie($get_path);
        }
        header('Location: dashboard.php');
    } else {
        $message = 'Invalid Login';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <style type="text/css">
    body {
        font-family: Arial;
    }

    #frmLogin {
        padding: 20px 40px 40px 40px;
        background: #d7eeff;
        border: #acd4f1 1px solid;
        color: #333;
        border-radius: 2px;
        width: 300px;
    }

    .field-group {
        margin-top: 15px;
    }

    .input-field {
        padding: 12px 10px;
        width: 100%;
        border: #A3C3E7 1px solid;
        border-radius: 2px;
        margin-top: 5px
    }

    .form-submit-button {
        background: #3a96d6;
        border: 0;
        padding: 10px 0px;
        border-radius: 2px;
        color: #FFF;
        text-transform: uppercase;
        width: 100%;
    }

    .error-message {
        text-align: center;
        color: #FF0000;
    }
    </style>
</head>

<body>
    <form action="" method="post" id="frmLogin">
        <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>
        <div class="field-group">
            <div>
                <label for="login">Username</label>
            </div>
            <div>
                <input name="member_name" type="text" value="" id="login" class="input-field">
            </div>
        </div>
        <div class="field-group">
            <div>
                <label for="password">Password</label>
            </div>
            <div>
                <input name="member_password" id="password" type="password" value="" class="input-field">
            </div>
        </div>
        <div class="field-group">
            <div>
                <input type="checkbox" name="remember" id="remember"
                    <?php if (isset($_COOKIE['member_login'])) { ?> checked="checked"
                    <?php } ?> /> <label for="remember">Remember me</label>
            </div>
        </div>
        <div class="field-group">
            <div>
                <input type="submit" name="login" value="Login" class="form-submit-button"></span>
            </div>
        </div>
    </form>
</body>
</html>
