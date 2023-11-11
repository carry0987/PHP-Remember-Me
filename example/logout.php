<?php
require dirname(__DIR__).'/vendor/autoload.php';
// Just for example
use carry0987\RememberMe\Example\CookieHandler;

session_start();
$get_path = dirname($_SERVER['PHP_SELF']);

//Clear Session
unset($_SESSION['username']);
session_unset();
session_destroy();

//Clear cookies
CookieHandler::setPath($get_path);
CookieHandler::clearAuthCookie();

header('Location: ./');
