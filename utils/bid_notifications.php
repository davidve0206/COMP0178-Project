<?php
require_once("console_log.php");
require_once("mailer.php");
require_once("store_notifications.php");

function bid_notifications($bidderId, $bidderEmail, $listing_name, $currentPrice, $newPrice, $item_id, mysqli $db, Mailer $mailer)
{
    // First we're sending notificaitons to the person who's just been outbid in place_bid.php
    // Using the above variables defined there
    if (isset($bidderId)) {
        // Debugging code
        // echo "$userId";
        // echo "$bidderEmail";

        // Extracting the results of the query
        // $row = $result->fetch_assoc();
        // $userId = $row["userId"];
        // $bidderEmail = $row['email'];

        // Sending an email to the outbid bidder
        $bidderSubject = "You have been outbid";
        $bidderMessage = "The new bid on ''$listing_name'' is $newPrice pounds.";
        $mailer->sendEmail($bidderEmail, $bidderSubject, $bidderMessage);

        // Storing a notification for the outbid bidder 
        $notifications_query_values = [];
        $notifications_query_values[] = "($bidderId, '$bidderSubject', '$bidderMessage')";
        store_notifications($db, $notifications_query_values);

        // Registering the bid as no longer being the highest bid
        $update_bid = "UPDATE Bids SET isHighest = 0 WHERE isHighest = 1 AND itemId = $item_id";
        $db->query($update_bid);
    }
    // Next we're making a query to see who's following the listed item, 

    // Query to extract information to notify people following the item that there's a new bid
    $query = "SELECT f.userId AS followerId, bidderId , u.email, MAX(b.isHighest) as isHighest
    FROM Items i
    LEFT JOIN Bids b ON b.itemId = i.id
    LEFT JOIN FollowedItems f ON i.id = f.itemId 
    LEFT JOIN Users u ON f.userId = u.id
    WHERE i.id = $item_id
    GROUP BY i.id, bidderid";
    $followerInformation = $db->query($query);

    // Loop to send followers information
    while ($row = $followerInformation->fetch_assoc()) {
        // Pull information from the current row
        $followerId = $row["followerId"];
        $bidderId = $row["bidderId"];
        $followerEmail = $row['email'];
        $isHighest = $row['isHighest'];

        if ($isHighest == 1 && $followerId == $bidderId) {
            // Skip this iteration of the loop as the bidder will be notified by the outbid functionality
        } elseif ($bidderId == $followerId) {

            // Send an email to followers
            $followerSubject = "New bid on your followed item: ''$listing_name''";
            $followerMessage = "There has been a new bid on ''$listing_name'' of $newPrice pounds. The previous bid on this item was $currentPrice pounds.";
            $mailer->sendEmail($followerEmail, $followerSubject, $followerMessage);

            // Store a notification for followers 
            $notifications_query_values = [];
            $notifications_query_values[] = "($followerId, '$followerSubject', '$followerMessage')";
            store_notifications($db, $notifications_query_values);
        }
        // TODO: We'll want another type of email to send to the person who's placing the bid.


        // TODO: I'm not going to cover this here, but we need to notify people who list the item that there are bids on them
        // I think the best way to do this is to add the item to the watchlist when the listing is created, so that these 
        // people can opt out of being notified.
    }
}
