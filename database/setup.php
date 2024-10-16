<?php
//TODO: move database login info to .env
//TODO: do we want to create a custom user for the db and use that in the rest of the app? (for polish)

$db = new mysqli(
    "localhost",
    "root",
);

if ($db->connect_error) {
    echo "Failed to connect to MySQL: " . $db->connect_error;
    exit();
}

// Comment this out if you dont want to have a fresh database
include_once("migrations.php");
migate($db);
