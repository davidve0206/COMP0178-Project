<?php include_once("header.php") ?>

<div class="container my-5">

    <?php

    // This function takes the form data and adds the new auction to the database.

    // Connect to MySQL database 
    require_once "/xampp/htdocs/COMP0178-Project/database/setup.php";

    /* Extract form data into variables, checking that they exist if required */

    $error_message = null;

    // Title
    if (!isset($_POST['auctionTitle']) or trim($_POST['auctionTitle']) == '') {
        $error_message = 'You must add a title.';
    } else {
        $title = $db->real_escape_string($_POST['auctionTitle']);
    }

    // Description
    if (!isset($_POST['auctionDescription']) or trim($_POST['auctionDescription']) == '') {
        echo $_POST['auctionDescription'];
        $error_message = 'Please add a description.';
    } else {
        $description = $db->real_escape_string($_POST['auctionDescription']);
    }

    // Category
    if (!isset($_POST['auctionCategory'])) {
        $error_message = 'You must select a category.';
    } else {
        $category_id = intval($_POST['auctionCategory']);
    }

    // Start Price
    if (isset($_POST['auctionStartPrice']) && $_POST['auctionStartPrice'] !== '') {
        $start_price = floatval($_POST['auctionStartPrice']);
    } else {
        $error_message = 'You must add a start price.';
    }

    // Reserve Price
    $reserve_price = isset($_POST['auctionReservePrice']) && $_POST['auctionReservePrice'] !== '' ? floatval($_POST['auctionReservePrice']) : null;

    // Start Date
    $start_date = isset($_POST['auctionStartDate']) && $_POST['auctionStartDate'] !== '' ? $db->real_escape_string($_POST['auctionStartDate']) : null;

    // End Date
    if (isset($_POST['auctionEndDate'])) {
        $end_date = $db->real_escape_string($_POST['auctionEndDate']);
    } else {
        $error_message = 'You must set an end date.';
    }

    // Seller
    if (!isset($_SESSION['userId'])) {
        $error_message = 'You must be logged in to create an auction.';
    } else {
        $seller_id = intval($_SESSION['userId']);
    }


    /* Function to perform extra checking on the data to make sure it can be inserted into the database. */

    function data_checking($title, $description, $category_id, $start_price, $reserve_price, $start_date, $end_date, $seller_id)
    {

        if (strlen($title) > 52) {
            return 'Title too long.';
        }

        if (strlen($description) > 257) {
            return 'description too long. Please limit it to 255 characters.';
        }

        if (!is_int($category_id)) {
            return 'Invalid category.';
        }

        if (!is_int($seller_id)) {
            return 'Invalid seller ID.';
        }

        if ($start_price <= 0) {
            return 'The starting price must be greater than 0.';
        }

        if (!is_null($reserve_price) && $reserve_price <= $start_price) {
            return 'The reserve price must be higher than the starting price.';
        }

        if (new DateTime($end_date) <= new DateTime()) {
            return 'The auction must end later than the current time.';
        }

        if (!is_null($start_date) && new DateTime($start_date) < new DateTime()) {
            return 'The start date for the auction cannot be in the past.';
        }

        if (!is_null($start_date) && new DateTime($end_date) < new DateTime($start_date)) {
            return 'The auction cannot end before it has started.';
        }

        return false;
    }

// Check for error message or invalid data

    if ($error_message) {
        echo <<<EOM
          <p>Error: $error_message</p>
          <button onclick="history.back()" class="btn btn-primary">Go Back</button>
EOM;
    } else {

        $invalid_data = data_checking($title, $description, $category_id, $start_price, $reserve_price, $start_date, $end_date, $seller_id);

        if ($invalid_data) {
            echo <<<EOM
          <p>Data is invalid: $invalid_data</p>
          <button onclick="history.back()" class="btn btn-primary">Go Back</button>
EOM;
        } else {
            /* If everything looks good, make the appropriate call to insert data into the database. */
            $db->query("USE auction_site");

            // Prepare the base query 
            $query = "INSERT INTO Items (itemName, description, sellerId, categoryId, startPrice, reservePrice, startDate, endDate)";
            $values = "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare("$query $values");
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
    }

    ?>

</div>


<?php include_once("footer.php") ?>