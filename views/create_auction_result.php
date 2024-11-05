<?php include_once("header.php") ?>

<div class="container my-5">

    <?php

    // This function takes the form data and adds the new auction to the database.

    // Connect to MySQL database 
    require_once "../database/setup.php";

    /* Extract form data into variables, checking that they exist if required */

    $error_messages = [];

    // Title
    if (!isset($_POST['auctionTitle']) or trim($_POST['auctionTitle']) == '') {
        array_push($error_messages, 'You must add a title.');
    } else {
        $title = $db->real_escape_string($_POST['auctionTitle']);

        if (strlen($title) > 52) {
            array_push($error_messages, 'Title too long.');
        }
    }

    // Description
    if (!isset($_POST['auctionDescription']) or trim($_POST['auctionDescription']) == '') {
        array_push($error_messages, 'Please add a description.');
    } else {
        $description = $db->real_escape_string($_POST['auctionDescription']);

        if (strlen($description) > 257) {
            array_push($error_messages, 'Description too long. Please limit it to 255 characters.');
        }
    }

    // Category
    if (!isset($_POST['auctionCategory'])) {
        array_push($error_messages, 'You must select a category.');
    } else {
        $category_id = intval($_POST['auctionCategory']);

        // If something is passed which is not an integer, intval will return 0
        if ($category_id == 0) {
            array_push($error_messages, 'Invalid category.');
        }
    }

    // Start Price
    if (isset($_POST['auctionStartPrice']) && $_POST['auctionStartPrice'] !== '') {
        $start_price = floatval($_POST['auctionStartPrice']);

        if ($start_price <= 0) {
            array_push($error_messages, 'The starting price must be greater than 0.');
        }
    } else {
        array_push($error_messages, 'You must add a start price.');
    }

    // Reserve Price
    $reserve_price = isset($_POST['auctionReservePrice']) && $_POST['auctionReservePrice'] !== '' ? floatval($_POST['auctionReservePrice']) : null;

    if (!is_null($reserve_price) && $reserve_price <= $start_price) {
        array_push($error_messages, 'The reserve price must be higher than the starting price.');
    }

    // Start Date
    $start_date = isset($_POST['auctionStartDate']) && $_POST['auctionStartDate'] !== '' ? $db->real_escape_string($_POST['auctionStartDate']) : null;

    if (!is_null($start_date) && new DateTime($start_date) < new DateTime()) {
        array_push($error_messages, 'The start date for the auction cannot be in the past.');
    }

    // End Date
    if (isset($_POST['auctionEndDate'])) {
        $end_date = $db->real_escape_string($_POST['auctionEndDate']);

        if (new DateTime($end_date) <= new DateTime()) {
            array_push($error_messages, 'The auction must end later than the current time.');
        }

        if (!is_null($start_date) && new DateTime($end_date) < new DateTime($start_date)) {
            array_push($error_messages, 'The auction cannot end before it has started.');
        }
    } else {
        array_push($error_messages, 'You must set an end date.');
    }

    // Seller
    if (!isset($_SESSION['userId'])) {
        array_push($error_messages, 'You must be logged in to create an auction.');
    } else {
        $seller_id = intval($_SESSION['userId']);

        if ($seller_id == 0) {
            array_push($error_messages, 'Invalid seller ID.');
        }
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

        // Prepare the base query 
        $query = "INSERT INTO Items (itemName, description, sellerId, categoryId, startPrice, reservePrice, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssiiddss", $title, $description, $seller_id, $category_id, $start_price, $reserve_price, $start_date, $end_date);

        // // Copilot code to log the query that is passed to the statement
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

    ?>

</div>


<?php include_once("footer.php") ?>