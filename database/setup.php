<?php
//TODO: move to .env
$db_name = "auction_site";

$mysqli = new mysqli(
    "localhost",
    "root",
    );

if ($mysqli -> connect_error) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

// First, delete the existing  database
$delete_db_statement = "DROP DATABASE IF EXISTS $db_name";
$delete_result = $mysqli->query($delete_db_statement);


// Then create a new database
$create_db_statement = "
CREATE DATABASE $db_name
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;";
$create_db_result = mysqli_query($mysqli, $create_db_statement);

// Use the newly created database
$use_db_statement = "USE $db_name";
$use_result = $mysqli->query($use_db_statement);

// Create the tables we need
$create_table_statement = "
CREATE TABLE Users
(
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
)";

$create_table_result = mysqli_query($mysqli, $create_table_statement);

//$mysqli->close();
?>