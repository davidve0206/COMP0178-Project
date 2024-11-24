<?php

/**
 * Uploads some default information on the database
 * TO BE IMPLEMENTED
 * @param mysqli $db
 * @return void
 */
function seed(mysqli $db)
{

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
        (4, 'PlayStation', 'PlayStation games and consoles'),
        (5, 'Sega', 'Sega vintage games and consoles'),
        (6, 'XBox', 'XBox games and consoles')
    ";
    $db->query($categories_creation_statement);

    $items_creation_statement = "
    INSERT INTO Items (id, itemName, description, sellerId, categoryId, startPrice, reservePrice, endDate, imagePath)
    VALUES
        (1, 'Pac-Man', 'Arcade slot station, modded so you dont need coins', 2, 1, 100, 120, '2024-12-12 12:00:00', '../images/pacman.webp'),
        (2, 'Mega Drive', 'Lightly used Sega Mega Drive, no games', 2, 5, 20, 35, '2024-12-12 12:00:00', '../images/mega_drive.jpg'),
        (3, 'Doom', 'Original Doom game CD-Rom, working', 3, 3, 200, 300, '2024-12-12 12:00:00', '../images/doom.webp'),
        (4, 'Outer Wilds', 'Space Exploration Mystery', 2, 4, 15, 25, '2024-11-12 12:00:00', '../images/outer_wilds.jpg'), 
        (5, 'Portal 2', 'First-person sci-fi puzzle, original disc', 3, 3, 55, 70, '2024-11-20 12:00:00', '../images/portal2.webp'),
        (6, 'Super Mario 64', '3D Platformer, used cartridge in good condition', 3, 2, 150, 160, '2024-11-23 12:00:00', '../images/super_mario64.webp'),
        (7, 'Red Dead Redemption 2', 'Western shooting adventure, second-hand blu-ray disc', 2, 6, 33, 38, '2024-11-25 12:00:00', '../images/rdr2.jpg'),
        (8, 'Grand Theft Auto: Vice City', 'Build a criminal empire in this open-world action-adventure, condition as new', 2, 4, 85, 90, '2024-11-29 12:00:00', '../images/gta_vice_city.jpg'),
        (9, 'Halo', 'First-person shooter set in the 26th century', 3, 6, 50, 60, '2024-12-02 12:00:00', '../images/halo.jpeg'),
        (10, 'The Last of Us', 'Post-apocalyptic action-adventure', 2, 4, 44, 49, '2024-12-07 12:00:00', '../images/the_last_of_us.webp'),
        (11, 'Bloodborne', 'Gothic horror RPG, original disc', 3, 4, 60, 65, '2024-12-11 12:00:00', '../images/bloodborne.png'),
        (12, 'Tetris', 'Tile-matching puzzle game, vintage edition', 2, 1, 149, 200, '2024-12-15 12:00:00', '../images/tetris_vintage.gif')
    ";
    $db->query($items_creation_statement);

    # TODO: when we create a functionality to create bids, we should consume it here instead of creating them by hand
    # this will ensure any expected behaviours (i.e., auto-adding to watched list)
    $bids_creation_statement = "
    INSERT INTO Bids (bidderId, itemId, bidPrice, isHighest, isWinner)
    VALUES
        (1, 1, 105, 0, 0),
        (1, 3, 250, 1, 0),
        (3, 1, 110, 1, 0),
        (3, 12, 151, 1, 0)
        (1, 3, 250, 0, 0),
        (3, 1, 110, 1, 0),
        (3, 3, 251, 0, 0),
        (1, 3, 252, 0, 0),
        (3, 3, 253, 1, 0),
        (1, 4, 26, 0, 0)
    ";
    $db->query($bids_creation_statement);

    $create_followed_statement = "
    INSERT INTO FollowedItems (userId, itemId)
    VALUES
        (1, 1),
        (1, 2),
        (1, 3),
        (1, 4),
        (3, 1),
        (3, 4)
    ";
    $db->query($create_followed_statement);
}
