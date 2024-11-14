<?php
require_once "console_log.php";
require_once "mailer.php";
require_once "store_notifications.php";

function close_auctions(mysqli $db, Mailer $mailer) {
    $unclosed_auctions = $db->query("
    SELECT i.id, i.itemName, i.sellerId, u.email, i.reservePrice
    FROM Items i JOIN Users u ON i.sellerId = u.id 
    WHERE i.isClosed = False AND i.endDate < NOW()");

    $notifications_query_values = [];
    while ($row = $unclosed_auctions->fetch_assoc()) {  
        // Pull information from the row
        $item_id = $row['id'];
        $item_name = $row['itemName'];
        $seller_id = $row['sellerId'];
        $seller_email = $row['email'];
        $reserve_price = $row['reservePrice'];
        
        // Get the highest bid for the item and check if they are a winner
        $highest_bid = $db->query("
        SELECT b.bidderId, b.bidPrice, u.email
        FROM Bids b JOIN Users u ON b.bidderId = u.id 
        WHERE b.itemId = $item_id ORDER BY b.bidPrice DESC LIMIT 1
        ")->fetch_assoc();

        // If the auction has a winner, send notifications to both the winner and the seller
        // and update the bid to be a winner, else, just notify the seller
        if ($highest_bid && $highest_bid['bidPrice'] >= $reserve_price) {
            // Send email to the seller
            $seller_subject = "Auction Closed";
            $seller_message = "The auction for item $item_name has closed. The winning bid was {$highest_bid['bidPrice']}.";
            $mailer->sendEmail($seller_email, $seller_subject, $seller_message);
            $notifications_query_values[] = "(?, $seller_id, '$seller_subject', '$seller_message')";

            // Send email to the winner
            $winner_subject = "You Won!";
            $winner_message = "Congratulations! You won the auction for item $item_name with a bid of {$highest_bid['bidPrice']}.";
            $mailer->sendEmail($highest_bid['email'], $winner_subject, $winner_message);
            $notifications_query_values[] = "({$highest_bid['bidderId']}, '$winner_subject', '$winner_message')";

            // Update the bid to be a winner
            $db->query("UPDATE Bids SET isWinner = True WHERE itemId = $item_id AND bidderId = {$highest_bid['bidderId']} AND bidPrice = {$highest_bid['bidPrice']}");
        } else {
            // Send email to the seller
            $seller_subject = "Auction Closed";
            $seller_message = "The auction for item $item_name has closed without a winner. " . ($highest_bid ? "The highest bid was {$highest_bid['bidPrice']}." : "There were no bids." );
            $mailer->sendEmail($seller_email, $seller_subject, $seller_message);
            $notifications_query_values[] = "($seller_id, '$seller_subject', '$seller_message')";
        }

        // Then send emails to every other user that has followed the item
        $followers = $db->query("SELECT u.id, u.email FROM FollowedItems fi JOIN Users u ON fi.userId = u.id WHERE fi.itemId = $item_id");
        while ($follower_row = $followers->fetch_assoc()) {
            $follower_subject = "Auction Closed";
            $follower_message = "The auction for item $item_name, which you were watching, has closed.";
            $mailer->sendEmail($follower_row['email'], $follower_subject, $follower_message);
            $notifications_query_values[] = "({$follower_row['id']}, '$follower_subject', '$follower_message')";   
        }

        // Close by adding the notifications to the table and updating the item to be closed
        store_notifications($db, $notifications_query_values);
        $db->query("UPDATE Items SET isClosed = True WHERE id = $item_id");
    }
}

// The file calls itself, but needs db setup to be present already
close_auctions($db, $mailer);

?>