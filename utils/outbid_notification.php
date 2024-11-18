<?php
require_once "console_log.php";
require_once "mailer.php";
require_once "store_notifications.php";

function bidder_outbid(mysqli $db, Mailer $mailer)
{
    // Selecting and saving the Id the bidder who has just been outbid
    $select_userId = "SELECT bidderId AS userId, id AS bidId
    FROM Bids WHERE isWinner = 1";
    $db->query($select_userId);
    $userId = $row["userId"];
    // CHECK: Check that the above way of formatting a query works
    // QUESTION: it fine if I use 1s and 0s, or should I be using True and False 

    // Selecting and saving their email as well 
    $select_userEmail = "SELECT email 
    FROM Users WHERE id = $userId";
    $db->query($select_userEmail);
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
