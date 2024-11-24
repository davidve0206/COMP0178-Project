<?php
require_once "../utils/verbose_errors.php";
require_once "../database/setup.php";

// Ensure there is at least a rating
if (isset($_POST["rating"])) {
    
    $rating = $_POST["rating"];
    $comment = isset($_POST["comment"]) ? $_POST["comment"] : "No comment";
    $sellerId = $_POST["sellerId"];
    $itemId = $_POST["itemId"];
    
    $rating_query = "
        INSERT INTO SellerRatings (sellerId, itemId, rating, comment)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        rating = VALUES(rating),
        comment = VALUES(comment)
    ";
    
    $stmt = $db->prepare($rating_query);
    $stmt->bind_param("iiis", $sellerId, $itemId, $rating, $comment);
    $query_result = $stmt->execute();
    
    // If we found something in the database, create a session with the user's info
    if ($query_result) {
        echo('<div class="text-center">Rating submitted! You will be redirected shortly.</div>');
    }
}

// Always redirect to browse
header("refresh:2; url=browse.php");