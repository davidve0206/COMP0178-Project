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
    $itemNumber = $_POST["listingInformation"];
} else {
    die("Error: Item ID is not set or is empty.");
}
//  Next, the queries into the database to get information (where relevant) for the variables

$query = "SELECT itemName, endDate, IFNULL(bidPrice, startPrice) AS currentPrice, 
bidderId, u.email AS buyerEmail, sellerId, s.email AS sellerEmail
    FROM Items i 
    LEFT JOIN Bids b ON i.id = b.itemId 
    LEFT JOIN Users u ON u.id = b.bidderId
    LEFT JOIN Users s ON s.id = i.sellerId
    WHERE i.id = ? AND ((
        SELECT max(bidPrice) as highestBid
        FROM Bids h
        WHERE itemId = ?
        GROUP BY itemId
    ) = bidPrice OR bidPrice IS NULL)";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $itemNumber, $itemNumber);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Now assigning the returned results into variables 
$itemName = $row['itemName'];
$endDate = $row['endDate'];
$currentPrice = $row['currentPrice'];
$previousBidderId = $row['bidderId'];
$previousBidderEmail = $row['buyerEmail'];
$sellerId = $row['sellerId'];
$sellerEmail = $row['sellerEmail'];

// Checking that the queries are valid before inserting data into the database
$error_messages = [];

// Current bidder: valid session
if (!isset($_SESSION['userId'])) {
    array_push($error_messages, 'You must be logged in to bid on an auction.');
} elseif ((isset($_SESSION['isSeller']) && $_SESSION['isSeller']) && !(isset($_SESSION['isBuyer']) && $_SESSION['isBuyer'])) {
    array_push($error_messages, 'You must be registered as a buyer to bid as an auction');
} else {
    $currentBidderId = intval($_SESSION['userId']);
    if ($currentBidderId == 0) {
        array_push($error_messages, 'Invalid bidder ID.');
    }
}
// Current bidder: valid bidder 
if ($currentBidderId == $previousBidderId) {
    array_push($error_messages, 'You already have the highest bid for this item.');
}
if ($currentBidderId == $sellerId) {
    array_push($error_messages, 'You cannot bid on an item that you listed.');
}

// bidPrice
if ($_POST["bid"] < $currentPrice + 1) {
    array_push($error_messages, 'Your bid must be more than a pound higher than the current value.');
    // (Fake) TODO: Make the increase in increment correspond to item value, so that a higher value item needs to have a greater bid
} else {
    // Update both the bid price and bid winner as they both depend on the bid being bigger than the last
    $newPrice = $_POST["bid"];
    $bid_winner = "1";
}

// bidDate
$now = time();
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

    bid_notifications(
        $currentBidderId,
        $previousBidderId,
        $previousBidderEmail,
        $sellerId,
        $sellerEmail,
        $itemName,
        $currentPrice,
        $newPrice,
        $itemNumber,
        $db,
        $mailer
    );
    // CHECK: I feel like we might need exception handling here, to make sure we don't accidentally end up in limbo, where there's
    // no bid marked as the winning bid

    // Prepare the base query 
    $query = "INSERT INTO Bids (bidderId, itemId, bidPrice) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iid", $currentBidderId, $itemNumber, $newPrice);
    if ($stmt->execute()) {
        echo ('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
    } else {
        echo 'Error making Create Auction query' . $stmt->error;
    }
    $stmt->close();
    $db->close();
}

header("refresh:2; url=browse.php");
