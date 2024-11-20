<?php
require_once("console_log.php");
require_once("mailer.php");
require_once("store_notifications.php");

function bid_notifications($listing_name, $new_bid, $item_id, mysqli $db, Mailer $mailer)
{

    // Selecting and saving the Id the bidder who has just been outbid
    $query = "SELECT bidderId AS userId, id AS bidId, email
    FROM Bids b INNER JOIN users u ON b.bidderId = u.id
    WHERE isHighest = 1";
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    $userId = $row["userId"];
    $bidderEmail = $row['email'];


    // Sending an email to the outbid bidder
    $bidderSubject = "You've been outbid";
    $bidderMessage = "The new bid on (insert listing item) is (insert bid price) ";
    $mailer->sendEmail($bidderSubject, $bidderMessage, $bidderEmail);
    // TODO: it'll be worth including the name of the listing, and the amount of the new bid in the email
    // I don't want to do that until I've standardised all the versions around this file 

    // Storing a notification for the outbid bidder 
    $notifications_query_values = [];
    $notifications_query_values[] = "($userId, $bidderSubject, $bidderEmail)";
    store_notifications($db, $notifications_query_values);
    // CHECK: I'm sure this can be condensed into a line... 

    // Registering the bid as no longer being the winning bid
    $update_bid = "UPDATE bids SET isWinner = 0 FROM bids WHERE bidId=id";
    $db->query($update_bid);
}

// What do we need to do for the people following the listing? For now, we're treating this bid as independent from the above
// Run a query through everyone in the watchlist  
// Save as variables in memory
// To each person, send an email and store a notification
// I think that's it, we're not changing the database in any way with this set of actions