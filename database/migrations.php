<?php

/**
 * Creates a fresh version of the dabase, deleteing your current version
 * @param mysqli $db
 * @return void
 */
function migrate(mysqli $db)
{
    $db = new mysqli(
        "localhost",
        "root",
    );

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
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    email VARCHAR(30) NOT NULL,
    firstName VARCHAR(20) NOT NULL,
    lastName VARCHAR(30) NOT NULL,
    address VARCHAR(100) NOT NULL,
    userJoinTime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    isBuyer BOOLEAN NOT NULL DEFAULT True, 
    isSeller BOOLEAN NOT NULL DEFAULT False 
    )";
    $db->query($create_users_statement);

    $create_categories_statement = "
    CREATE TABLE Categories
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255)
    )";
    $db->query($create_categories_statement);

    $create_items_statement = "
    CREATE TABLE Items
    (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    itemName VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    sellerId INT NOT NULL,
    categoryId INT NOT NULL,
    startPrice DECIMAL(7,2) NOT NULL,
    reservePrice DECIMAL(7,2) CHECK (reservePrice > startPrice),
    startDate TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    endDate TIMESTAMP(0) NOT NULL CHECK (endDate > startDate),
    FOREIGN KEY (sellerId) REFERENCES Users(id),
    FOREIGN KEY (categoryId) REFERENCES Categories(id)
    )";
    $db->query($create_items_statement);
 
    $create_bids_statement = "
    CREATE TABLE Bids
    (
    bidderId INT NOT NULL,
    itemId INT NOT NULL,
    bidPrice DECIMAL(7,2) NOT NULL,
    bidDate TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    isWinner BOOL DEFAULT 0,
    FOREIGN KEY (bidderId) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (itemId) REFERENCES Items(id) ON DELETE CASCADE,
    CONSTRAINT PK_Bids PRIMARY KEY (bidderId, itemId, bidPrice)
    )";
    $db->query($create_bids_statement);
    
    $create_followed_statement = "
    CREATE TABLE FollowedItems
    (
    userId INT NOT NULL,
    itemId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (itemId) REFERENCES Items(id) ON DELETE CASCADE
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

    $db->close();
}
