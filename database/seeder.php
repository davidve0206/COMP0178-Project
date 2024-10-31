<?php

/**
 * Uploads some default information on the database
 * TO BE IMPLEMENTED
 * @param mysqli $db
 * @return void
 */
function seed(mysqli $db) {
    
    # I am using my personal email in case we need to test, feel free to change for your own if you are testing that functionality
    # TODO: implement password hashing when we do it for the rest of the app... for now its just a text
    
    $user_creation_statement = "
    INSERT INTO Users (id, username, password, email, firstName, lastName, address, isBuyer, isSeller)
    VALUES
        (1, 'buyer1', 'password', 'uclauctionsite2024g27+1@gmail.com', 'BuyerAdam', 'BuyerSmith', '123 UCL, London', True, False),
        (2, 'seller1', 'password', 'uclauctionsite2024g27+2@gmail.com', 'SellerJhon', 'SellerKeynes', '456 UCL, London', False, True),
        (3, 'mixed1', 'password', 'uclauctionsite2024g27+3@gmail.com', 'MixedDavid', 'MixedRicardo', '789 UCL, London', True, True)
    ";
    $db->query($user_creation_statement);

    $categories_creation_statement = "
    INSERT INTO Categories (id, name, description)
    VALUES
        (1, 'Arcade', 'Arcade videogames and machines, slot operated or modded'),
        (2, 'Nintendo', 'Nintendo consoles and any games for them, independent of generation'),
        (3, 'PC', 'Gaming PCs, be it laptops or desktop, and asociated games'),
        (4, 'PlayStatation', 'PlayStation games and consoles'),
        (5, 'Sega', 'Sega vintage games and consoles'),
        (6, 'XBox', 'XBox games and consoles')
    ";
    $db->query($categories_creation_statement);

    $items_creation_statement = "
    INSERT INTO Items (id, itemName, description, sellerId, categoryId, startPrice, reservePrice, endDate)
    VALUES
        (1, 'Pac-Man', 'Arcade slot station, modded so you dont need coins', 2, 1, 100, 120, '2024-12-12 12:00:00'),
        (2, 'Mega Drive', 'Lightly used Sega Mega Drive, no games', 2, 5, 20, 35, '2024-12-12 12:00:00'),
        (3, 'Doom', 'Original Doom game CD-Rom, working', 3, 3, 200, 300, '2024-12-12 12:00:00')
    ";
    $db->query($items_creation_statement);

    # TODO: when we create a functionality to create bids, we should consume it here instead of creating them by hand
    # this will ensure any expected behaviours (i.e., auto-adding to watched list)
    $bids_creation_statement = "
    INSERT INTO Bids (bidderId, itemId, bidPrice)
    VALUES
        (1, 1, 105),
        (1, 3, 250),
        (3, 1, 110)
    ";
    $db->query($bids_creation_statement);

    $create_followed_statement = "
    INSERT INTO FollowedItems (userId, itemId)
    VALUES
        (1, 1),
        (1, 2),
        (1, 3),
        (3, 1)
    ";
    $db->query($create_followed_statement);
}
