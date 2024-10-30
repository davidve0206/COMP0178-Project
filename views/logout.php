<?php

session_start();

unset($_SESSION['loggedIn']);
unset($_SESSION['userId']);
unset($_SESSION['username']);
unset($_SESSION['email']);
unset($_SESSION['isBuyer']);
unset($_SESSION['isSeller']);
setcookie(session_name(), "", time() - 360);
session_destroy();

// Redirect to browse
header("Location: browse.php");

?>