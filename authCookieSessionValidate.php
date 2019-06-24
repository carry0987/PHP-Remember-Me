<?php 
require dirname(__FILE__).'/class/Auth.php';
require dirname(__FILE__).'/class/Util.php';

$auth = new Auth();
$db_handle = DBController::getInstance();
$get_path = dirname($_SERVER['PHP_SELF']);
$util = new Util($get_path);
//Get Current date, time
$current_time = time();

//Set Cookie expiration for 1 month
$cookie_expiration_time = $current_time + (30 * 24 * 60 * 60);
$isLoggedIn = false;

//Check if loggedin session and redirect if session exists
if (!empty($_SESSION['member_name'])) {
    $isLoggedIn = true;
}
//Check if loggedin session exists
elseif (!empty($_COOKIE['member_login']) && !empty($_COOKIE['random_password']) && !empty($_COOKIE['random_selector'])) {
    //Initiate auth token verification diirective to false
    $isPasswordVerified = false;
    $isExpiryDateVerified = false;

    //Get token for username
    $selector = (isset($_COOKIE['random_selector'])) ? $_COOKIE['random_selector'] : 0;
    $userToken = $auth->getTokenByUserID($_COOKIE['member_login'], $selector);

    //Validate random password cookie with database
    if (password_verify($_COOKIE['random_password'], $userToken[0]['password_hash'])) {
        $isPasswordVerified = true;
    }

    //Check cookie expiration by date
    if ($userToken[0]['expiry_date'] >= $current_time) {
        $isExpiryDareVerified = true;
    }

    //Redirect if all cookie based validation retuens true
    //Else, mark the token as expired and clear cookies
    if ($userToken !== false && $isPasswordVerified && $isExpiryDareVerified) {
        $isLoggedIn = true;
    } else {
        if ($userToken !== false) {
            $auth->markAsExpired($userToken[0]['selector_hash']);
        }
        //Clear cookies
        $util->clearAuthCookie();
    }
}
