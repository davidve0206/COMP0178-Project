<?php

// 
/* Extract form data into variables, checking that they exist if required */

// Check: I'm not sure I've worked out redirecting the user, per the TODO at the bottom. Worth working this out in the testing
// phase

// First, the queries into the database to get information (where relevant) to insert into the variables

$db->query("USE auction_site");
$query = "SELECT id, endDate, GREATEST(startPrice, IFNULL(MAX(bidPrice), startPrice)) AS currentPrice
FROM Items LEFT JOIN Bids ON Items.id = Bids.itemId ";

$query .= "WHERE id = $item_id";

// Now assigning the returning results into variables 

$item_id = $row['id'];
$endDate = new DateTime($row['endDate']);
$currentPrice = $row['currentPrice'];

// Checking for errors before inserting data into the database

$error_messages = [];

// bidderID
if (!isset($_SESSION['userId'])) {
    array_push($error_messages, 'You must be logged in to bid on an auction.');
} else {
    $bidder_id = intval($_SESSION['userId']);
    if ($bidder_id == 0) {
        array_push($error_messages, 'Invalid bidder ID.');
    }
}

// bidPrice

if ($_POST["bidNumber"] < $currentPrice + 1) {
    array_push($error_messages, 'Your bid must be more than a pound higher than the current value.');
    // TODO: In a real-life scenario, you'd probably want the increment to correspond to the item value, such that the greater the value, the greater the increment
} else {
    $bid_price = $_POST["bidNumber"];
}

// bidDate

$now = time();
if ($now > $endDate) {
    array_push($error_messages, 'Sorry, the auction has now expired.');
} else {
    $bid_date = $now;
}

// Check for error messages

if (count($error_messages) > 0) {

    foreach ($error_messages as $error) {
        echo "<p><span class='font-weight-bold'>Error: </span> $error</p>";
    }
    echo '<button onclick="history.back()" class="btn btn-primary">Go Back</button>';
} else {
    /* If everything looks good, make the appropriate call to insert data into the database. */
    $db->query("USE auction_site");
    // Question: I've specified this at the beginning file. That means that this is redundant, correct? 

    // Prepare the base query 
    $query = "INSERT INTO Bids (bidderId, itemId, bidPrice, bidDate) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iids", $bidder_id, $item_id, $bid_price, $bid_date);


    // Debugging code, adapted from Ben's. You'd need to change the variables to use it 

    //Copilot code to log the query that is passed to the statement
    // function bind_query($query, $params)
    // {
    //     foreach ($params as $param) {
    //         $query = preg_replace('/\?/', "'$param'", $query, 1);
    //     }
    //     return $query;
    // }

    // $logged_query = bind_query($query, $values);
    // echo $logged_query;

    if ($stmt->execute()) {
        echo ('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
    } else {
        echo 'Error making Create Auction query' . $stmt->error;
    }
    $stmt->close();
    $db->close();
}


// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.
