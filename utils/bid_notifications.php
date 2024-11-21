<?php
require_once("console_log.php");
require_once("mailer.php");
require_once("store_notifications.php");

function bid_notifications($listing_name, $bid_amount, $item_id, mysqli $db, Mailer $mailer)
{
    // Query to extract relevant information 
    $query = "SELECT bidderId AS userId, id AS bidId, email
    FROM Bids b INNER JOIN users u ON b.bidderId = u.id
    WHERE isHighest = 1 AND b.itemId = $item_id";
    $result = $db->query($query);

    // Checking to see whether the item has any bids on it, and if so sending notifications to the outbid bidder
    if ($result->num_rows > 0) {
        // Debugging code
        // echo "$userId";
        // echo "$bidderEmail";

        // Extracting the results of the query
        $row = $result->fetch_assoc();
        $userId = $row["userId"];
        $bidderEmail = $row['email'];
        $bidId = $row['bidId'];

        // Sending an email to the outbid bidder
        $bidderSubject = "You have been outbid";
        $bidderMessage = "The new bid on ''$listing_name'' is $bid_amount pounds.";
        $mailer->sendEmail($bidderEmail, $bidderSubject, $bidderMessage);

        // Storing a notification for the outbid bidder 
        $notifications_query_values = [];
        $notifications_query_values[] = "($userId, '$bidderSubject', '$bidderMessage')";
        store_notifications($db, $notifications_query_values);

        // Registering the bid as no longer being the highest bid
        $update_bid = "UPDATE Bids SET isHighest = 0 WHERE isHighest = 1 AND itemId = $item_id";
        $db->query($update_bid);
    } else {
    }
} 

// What do we need to do for the people following the listing? For now, we're treating this bid as independent from the above

// Run a query through everyone in the watchlist  
// Save as variables in memory
// To each person, send an email and store a notification
// I think that's it, we're not changing the database in any way with this set of actions

// There's currently an issue in that, if there's an error in registering a notification, the email saying that you're outbid 
// Still goes through