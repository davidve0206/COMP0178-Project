<?php

require_once "../database/setup.php";
$login_success = false;

// Check no user is already logged in and there is info in the request
if (!isset($_SESSION["user_id"]) && ($_POST["username"]) && isset($_POST["password"])) {
    
    // Read the loggin attempt from the request
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Find in the database
    $login_query = "
        SELECT id, username, email, isBuyer, isSeller
        FROM Users
        WHERE username = '$username' AND password = '$password'
    ";
    
    $query_result = $db->query($login_query);
    $login_result = $query_result->fetch_assoc();
    
    // If we found something in the database, create a session with the user's info
    if ($login_result) {
        session_start();
        $_SESSION['loggedIn'] = true;
        $_SESSION['userId'] = $login_result["id"];
        $_SESSION['username'] = $login_result["username"];
        $_SESSION['email'] = $login_result["email"];
        $_SESSION['isBuyer'] = $login_result["isBuyer"];
        $_SESSION['isSeller'] = $login_result["isSeller"];

        $login_success = true;
    }
}

// Show different messages if the loggin was a success or not
// TODO: Improve these views if we have extra time
if ($login_success) {
    echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
} else {
    echo('<div class="text-center">Invalid Username or Password. You will be redirected shortly.</div>');
}

// Always redirect to browse
header("refresh:2; url=browse.php");