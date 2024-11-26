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
    INSERT INTO Users (id, username, password, email, firstName, lastName, addressStreet, addressTown, addressCountry, addressPostcode, isBuyer, isSeller)
    VALUES
        (1, 'buyer1', 'password', 'uclauctionsite2024g27+1@gmail.com', 'Adam', 'Smith', '123 UCL', 'London', 'UK', 'WC1E', True, False),
        (2, 'buyer2', 'password', 'uclauctionsite2024g27+2@gmail.com', 'John', 'Hicks', '124 UCL', 'London', 'UK', 'WC1E', True, False),
        (3, 'buyer3', 'password', 'uclauctionsite2024g27+3@gmail.com', 'Milton', 'Friedman', '125 UCL', 'London', 'UK', 'WC1E', True, False),
        (4, 'buyer4', 'password', 'uclauctionsite2024g27+4@gmail.com', 'Paul', 'Samuelson', '126 UCL', 'London', 'UK', 'WC1E', True, False),
        (5, 'buyer5', 'password', 'uclauctionsite2024g27+5@gmail.com', 'Alfred', 'Marshall', '127 UCL', 'London', 'UK', 'WC1E', True, False),
        (6, 'seller1', 'password', 'uclauctionsite2024g27+6@gmail.com', 'Jhon', 'Keynes', '456 UCL', 'London', 'UK', 'WC1E', False, True),
        (7, 'seller2', 'password', 'uclauctionsite2024g27+7@gmail.com', 'Friedrich', 'Hayek', '457 UCL', 'London', 'UK', 'WC1E', False, True),
        (8, 'seller3', 'password', 'uclauctionsite2024g27+8@gmail.com', 'Joan', 'Robinson', '458 UCL', 'London', 'UK', 'WC1E', False, True),
        (9, 'mixed1', 'password', 'uclauctionsite2024g27+9@gmail.com', 'David', 'Ricardo', '789 UCL', 'London', 'UK', 'WC1E', True, True),
        (10, 'mixed2', 'password', 'uclauctionsite2024g27+10@gmail.com', 'Karl', 'Marx', '790 UCL', 'London', 'UK', 'WC1E', True, True)
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
    INSERT INTO Items (id, itemName, description, sellerId, categoryId, startPrice, reservePrice, startDate, endDate, imagePath)
    VALUES
        (1, 'Pac-Man', 'Arcade slot station, modded so you dont need coins', 6, 1, 100, 120, '2024-11-01 12:00:00', '2024-12-12 12:00:00', '../images/pacman.webp'),
        (2, 'Mega Drive', 'Lightly used Sega Mega Drive, no games', 6, 5, 20, 35, '2024-11-01 12:00:00', '2024-12-12 12:00:00', '../images/mega_drive.jpg'),
        (3, 'Doom', 'Original Doom game CD-Rom, working', 6, 3, 200, 300, '2024-11-01 12:00:00', '2024-12-12 12:00:00', '../images/doom.webp'),
        (4, 'Outer Wilds', 'Space Exploration Mystery', 7, 4, 15, 25, '2024-11-01 12:00:00', '2024-11-12 12:00:00', '../images/outer_wilds.jpg'), 
        (5, 'Portal 2', 'First-person sci-fi puzzle, original disc', 7, 3, 55, 70, '2024-11-01 12:00:00', '2024-11-20 12:00:00', '../images/portal2.webp'),
        (6, 'Super Mario 64', '3D Platformer, used cartridge in good condition', 7, 2, 150, 160, '2024-11-01 12:00:00', '2024-11-23 12:00:00', '../images/super_mario64.webp'),
        (7, 'Red Dead Redemption 2', 'Western shooting adventure, second-hand blu-ray disc', 8, 6, 33, 38, '2024-11-01 12:00:00', '2024-11-25 12:00:00', '../images/rdr2.jpg'),
        (8, 'Grand Theft Auto: Vice City', 'Build a criminal empire in this open-world action-adventure, condition as new', 8, 4, 85, 90, '2024-11-01 12:00:00', '2024-12-02 12:00:00', '../images/gta_vice_city.jpg'),
        (9, 'Halo', 'First-person shooter set in the 26th century', 9, 6, 50, 60, '2024-11-01 12:00:00', '2024-12-02 12:00:00', '../images/halo.jpeg'),
        (10, 'The Last of Us', 'Post-apocalyptic action-adventure', 9, 4, 44, 49, '2024-11-01 12:00:00', '2024-12-07 12:00:00', '../images/the_last_of_us.webp'),
        (11, 'Bloodborne', 'Gothic horror RPG, original disc', 10, 4, 60, 65, '2024-11-01 12:00:00', '2024-12-11 12:00:00', '../images/bloodborne.png'),
        (12, 'Tetris', 'Tile-matching puzzle game, vintage edition', 10, 1, 149, 200, '2024-11-01 12:00:00', '2024-12-15 12:00:00', '../images/tetris_vintage.gif')
    ";
    $db->query($items_creation_statement);

    # TODO: when we create a functionality to create bids, we should consume it here instead of creating them by hand
    # this will ensure any expected behaviours (i.e., auto-adding to watched list)
    $bids_creation_statement = "
    INSERT INTO Bids (bidderId, itemId, bidPrice, isHighest, isWinner)
    VALUES
        (1, 1, 105, 0, 0),
        (9, 1, 110, 0, 0),
        (2, 1, 115, 0, 0),
        (3, 1, 120, 0, 0),
        (4, 1, 125, 1, 0),
        (1, 3, 250, 0, 0),
        (9, 3, 251, 0, 0),
        (1, 3, 252, 0, 0),
        (9, 3, 253, 1, 0),
        (1, 4, 20, 0, 0),
        (2, 4, 22, 0, 0),
        (3, 4, 24, 0, 0),
        (1, 4, 26, 1, 1),
        (4, 5, 55, 0, 0),
        (2, 5, 65, 0, 0),
        (10, 5, 68, 0, 0),
        (10, 6, 150, 0, 0),
        (9, 6, 155, 0, 0),
        (10, 6, 161, 1, 1),
        (1, 8, 85, 0, 0),
        (9, 8, 88, 0, 0),
        (1, 8, 90, 1, 0),
        (10, 9, 50, 0, 0),
        (1, 9, 55, 0, 0),
        (10, 9, 60, 1, 0),
        (2, 10, 44, 0, 0),
        (4, 10, 47, 0, 0),
        (2, 10, 49, 1, 0),
        (1, 11, 60, 0, 0),
        (9, 11, 62, 0, 0),
        (1, 11, 65, 1, 0),
        (3, 12, 149, 0, 0),
        (4, 12, 160, 0, 0),
        (3, 12, 200, 1, 0)
    ";
    $db->query($bids_creation_statement);

    $create_followed_statement = "
    INSERT INTO FollowedItems (userId, itemId)
    VALUES
        (1, 1),
        (1, 2),
        (1, 3),
        (1, 4),
        (1, 5),
        (1, 6),
        (1, 7),
        (1, 8),
        (1, 9),
        (1, 10),
        (1, 11),
        (1, 12),
        (9, 1),
        (9, 4)
    ";
    $db->query($create_followed_statement);

    $create_ratings_statement = "
    INSERT INTO SellerRatings (sellerId, itemId, rating, comment, submittedTime)
    VALUES
        (7, 4, 4, 'Good seller, fast delivery', '2024-11-13 18:00:00'),
        (7, 6, 3, 'Delivered item in a worse state than expected but still functional', '2024-11-25 08:00:00')
    ";
    $db->query($create_ratings_statement);
}
