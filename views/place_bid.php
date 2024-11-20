<?php
require_once("../utils/verbose_errors.php");
require_once("../utils/console_log.php");
require_once("../utils/utilities.php");
require_once("../database/setup.php");
require_once("../utils/bid_notifications.php");
session_start();

/* Extract form data into variables, checking that they exist if required */

// Get the itemId from the submitted form 
if (isset($_POST["listingInformation"]) && !empty($_POST["listingInformation"])) {
    $item_id = $_POST["listingInformation"];
} else {
    die("Error: Item ID is not set or is empty.");
}

// Next, the queries into the database to get information (where relevant) for the variables

$query = "SELECT id, endDate, GREATEST(startPrice, IFNULL(MAX(bidPrice), startPrice)) AS currentPrice 
FROM Items LEFT JOIN Bids ON Items.id = Bids.itemId";
$query .= "WHERE id = $item_id";
$result = $db->query($query);
$row = $result->fetch_assoc();

// Now assigning the returning results into variables 

$endDate = new DateTime($row['endDate']);
$currentPrice = $row['currentPrice'];

// Checking for errors before inserting data into the database

$error_messages = [];

// bidderID
if (!isset($_SESSION['userId'])) {
    array_push($error_messages, 'You must be logged in to bid on an auction.');
}
if (isset($_SESSION['isSeller']) && $_SESSION['isSeller']) {
    array_push($error_messages, 'You must be registered as a buyer to bid as an auction');
} else {
    $bidder_id = intval($_SESSION['userId']);
    if ($bidder_id == 0) {
        array_push($error_messages, 'Invalid bidder ID.');
    }
}

// bidPrice
if ($_POST["bid"] < $currentPrice + 1) {
    array_push($error_messages, 'Your bid must be more than a pound higher than the current value.');
    // TODO: Make the increase in increment correspond to item value, so that a higher value item needs to have a greater bid
} else {
    // Update both the bid price and bid winner as they both depend on the bid being bigger than the last
    $bid_price = $_POST["bid"];
    $bid_winner = "1";
}

// bidDate
$now = new DateTime();
if ($now >= $endDate) {
    array_push($error_messages, 'Sorry, the auction has now expired.');
}


// Check for error messages
if (count($error_messages) > 0) {

    foreach ($error_messages as $error) {
        echo "<p><span class='font-weight-bold'>Error: </span> $error</p>";
    }
    echo '<button onclick="history.back()" class="btn btn-primary">Go Back</button>';
} else {
    /* If everything looks good, make the appropriate call to insert data into the database. */

    // First, notify people following the listings of the new bid

    // bid_notifications($item_id, $db, $mailer);

    // I feel like we might need exception handling here, to make sure we don't accidentally end up in limbo, where there's
    // no bid marked as the winning bid

    // Prepare the base query 
    $query = "INSERT INTO Bids (bidderId, itemId, isWinner, bidPrice) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iiid", $bidder_id, $item_id, $bid_winner, $bid_price);
    if ($stmt->execute()) {
        echo ('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
    } else {
        echo 'Error making Create Auction query' . $stmt->error;
    }
    $stmt->close();
    $db->close();
}

header("refresh:2; url=browse.php");
