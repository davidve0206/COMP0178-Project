<?php

// Make the db connection available as $db
$db = new mysqli(
    "localhost",
    "root",
    null,
    "auction_site"
);

if ($db->connect_error) {
    echo "Failed to connect to MySQL: " . $db->connect_error;
    exit();
}