<?php
require_once("console_log.php");
require_once("mailer.php");
require_once("store_notifications.php");

function bid_notifications(
    $currentBidderId,
    $previousBidderId,
    $bidderEmail,
    $sellerId,
    $sellerEmail,
    $listing_name,
    $currentPrice,
    $newPrice,
    $item_id,
    mysqli $db,
    Mailer $mailer
) {
    // We're sending the seller a notification of a new bid no matter what 

    // Send an email to the seller 
    $sellerSubject = "New bid on your listed item: ''$listing_name''";
    $sellerMessage = "There has been a new bid on your listed item ''$listing_name'' for $newPrice pounds. The previous bid on this item was $currentPrice pounds.";
    $mailer->sendEmail($sellerEmail, $sellerSubject, $sellerMessage);

    // Store a notification for the seller
    $notifications_query_values = [];
    $notifications_query_values[] = "($sellerId, '$sellerSubject', '$sellerMessage')";
    

    // We're sending notificaitons to the person who's just been outbid in place_bid.php
    if (isset($previousBidderId)) {
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

        // Add notification to the list of notifications to be stored
        $notifications_query_values[] = "($previousBidderId, '$bidderSubject', '$bidderMessage')";
    }
    // Next we're sending email to whoever might be following the bid

    // Query to extract information to notify people following the item that there's a new bid
    $query = "SELECT f.userId AS followerId, u.email
    FROM Items i
    LEFT JOIN FollowedItems f ON i.id = f.itemId
    LEFT JOIN Users u ON f.userId = u.id
    WHERE i.id = $item_id";
    $followerInformation = $db->query($query);
    // Loop to send followers information
    while ($row = $followerInformation->fetch_assoc()) {
        // Pull information from the current row
        $followerId = $row['followerId'];
        $followerEmail = $row['email'];

        if (!is_null($followerId)) {
            // The current bidder doesnt need to be notified and the previous bidder gets a different notification
            if ($currentBidderId != $followerId && $previousBidderId != $followerId) {

                // Send an email to followers
                $followerSubject = "New bid on your followed item: ''$listing_name''";
                $followerMessage = "There has been a new bid on ''$listing_name'' of $newPrice pounds. The previous bid on this item was $currentPrice pounds.";
                $mailer->sendEmail($followerEmail, $followerSubject, $followerMessage);

                // Add the notification to the list of notifications to be stored
                $notifications_query_values[] = "($followerId, '$followerSubject', '$followerMessage')";
            }
        }
    }
    // Store all notifications at once
    store_notifications($db, $notifications_query_values);

    // Registering the previous bid as no longer being the  highest bid
    $update_bid = "UPDATE Bids SET isHighest = 0 WHERE isHighest = 1 AND itemId = $item_id";
    $db->query($update_bid);
}
