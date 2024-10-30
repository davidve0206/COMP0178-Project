<?php
// This allow verbose errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Comment this out if you dont want to have a fresh database
require_once "database/migrations.php";
migrate($db);

// For now, index.php just redirects to browse.php, but you can change this
// if you like.
header("Location: views/browse.php");
