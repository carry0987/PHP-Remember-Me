<?php
session_start();
require dirname(__FILE__).'/class/Util.php';
$get_path = dirname($_SERVER['PHP_SELF']);
$util = new Util($get_path);

//Clear Session
unset($_SESSION['member_id']);
session_unset();
session_destroy();

//Clear cookies
$util->clearAuthCookie();

header('Location: ./');
