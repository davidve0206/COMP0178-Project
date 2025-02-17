<?php

/**
 * Creates a fresh version of the dabase, deleteing your current version
 * @param mysqli $db
 * @return void
 */
function migrate()
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
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    firstName VARCHAR(20) NOT NULL,
    lastName VARCHAR(30) NOT NULL,
    addressStreet VARCHAR(100) NOT NULL,
    addressTown VARCHAR(50) NOT NULL,
    addressCountry VARCHAR(50) NOT NULL,
    addressPostcode VARCHAR(10) NOT NULL,
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

    # I have deleted the check, but I'm still getting an error when endDate comes after startDate??
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
    endDate TIMESTAMP(0) NOT NULL,
    startDate TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    imagePath VARCHAR(255),
    isClosed BOOLEAN NOT NULL DEFAULT False,
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
    isHighest BOOL DEFAULT 1,
    isWinner BOOL DEFAULT 0,
    bidDate TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    FOREIGN KEY (bidderId) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (itemId) REFERENCES Items(id) ON DELETE CASCADE,
    CONSTRAINT PK_Bids PRIMARY KEY (itemId, bidPrice)
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
    userId INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message VARCHAR(255) NOT NULL,
    isRead BOOLEAN NOT NULL DEFAULT False,
    notificationTime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    FOREIGN KEY (userId) REFERENCES Users(id) ON DELETE CASCADE
    )";
    $db->query($create_notifications_statement);

    $create_ratings_statement = "
    CREATE TABLE SellerRatings
    (
    sellerId INT NOT NULL,
    itemId INT NOT NULL,
    rating INT NOT NULL,
    comment VARCHAR(255),
    submittedTime TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    FOREIGN KEY (sellerId) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (itemId) REFERENCES Items(id) ON DELETE CASCADE,
    CONSTRAINT unique_review_per_item UNIQUE (itemId)
    )";
    $db->query($create_ratings_statement);

    // Seed the fresh db with default data if you want
    require_once "seeder.php";
    seed($db);

    $db->close();
}
