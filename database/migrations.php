<?php

/**
 * Creates a fresh version of the dabase, deleteing your current version
 * @param mysqli $db
 * @return void
 */
function migate(mysqli $db)
{
    $db_name = "auction_site";

    // First, delete the existing database
    $db->query("DROP DATABASE IF EXISTS $db_name");

    // Then create a new database
    $db->query("
    CREATE DATABASE $db_name
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;");

    // Use the newly created database
    $use_db_statement = "USE $db_name";
    $db->query($use_db_statement);

    // Create the tables we need
    $create_users_statement = "
    CREATE TABLE Users
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_users_statement);

    $create_items_statement = "
    CREATE TABLE Items
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_items_statement);

    $create_categories_statement = "
    CREATE TABLE Categories
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_categories_statement);

    $create_bids_statement = "
    CREATE TABLE Bids
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_bids_statement);

    $create_followed_statement = "
    CREATE TABLE FollowedItems
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_followed_statement);

    $create_notifications_statement = "
    CREATE TABLE Notifications
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL
    )";
    $db->query($create_notifications_statement);

    // Seed the fresh db with default data if you want
    require_once "seeder.php";
    seed($db);
}
