<?php 
session_start();
require 'authCookieSessionValidate.php';
if (!$isLoggedIn) {
    header('Location: ./');
}
?>
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
<div class="member-dashboard">
    <span>You have Successfully logged in!. <a href="logout.php">Logout</a></span>
</div>