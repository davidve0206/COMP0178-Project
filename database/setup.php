<?php

//TODO: move database login info to .env
//TODO: do we want to create a custom user for the db and use that in the rest of the app? (for polish)

// Make the db connection available as $db
$db = new mysqli(
    "localhost",
    "root",
);

if ($db->connect_error) {
    echo "Failed to connect to MySQL: " . $db->connect_error;
    exit();
}