<?php

session_start();


$_SESSION = [];

// This Destroys the session
session_destroy();

//  This Redirects to the login page
header("Location: login.php");
exit;
?>