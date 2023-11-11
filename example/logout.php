<?php
require dirname(__DIR__).'/vendor/autoload.php';
use carry0987\RememberMe\RememberMe as RememberMe;

session_start();
$get_path = dirname($_SERVER['PHP_SELF']);

//Clear Session
unset($_SESSION['member_id']);
unset($_SESSION['member_name']);
session_unset();
session_destroy();

//Clear cookies
RememberMe::clearAuthCookie($get_path);

header('Location: ./');
