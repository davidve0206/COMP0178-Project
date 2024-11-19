<?php

/**
 * Stores notifications in the database
 * @param mysqli $db
 * @param array $notifications_query_values - an array of strings formated (userId, 'subject', 'message')
 */
function store_notifications(mysqli $db, array $notifications_query_values) {
    $notifications_query = "INSERT INTO Notifications (userId, subject, message) VALUES ";
    $notifications_query .= implode(", ", $notifications_query_values);
    $db->query($notifications_query);
}