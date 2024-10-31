<?php include_once("header.php") ?>

<div class="container my-5">

    <?php

    // This function takes the form data and adds the new auction to the database.

    // Connect to MySQL database 
    require_once "/xampp/htdocs/COMP0178-Project/database/setup.php"; # Q: Does this match everyone's setup?

    /* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */
    $title = $_POST['auctionTitle'];
    $details = $_POST['auctionDetails'];
    // $categoryId = $_POST['auctionCategory'];
    $categoryId = 1;
    $startPrice = $_POST['auctionStartPrice'];
    $reservePrice = $_POST['auctionReservePrice'];
    $endDate = $_POST['auctionEndDate'];
    $sellerId = 1; # Hard-coded for now until login is working
    # To-Do: Add start date on frontend + add logic to change the insert query if it is specified

    # Converting the date format into a MySQL timestamp
    $endDate_dateTime = new DateTime($endDate);
    $endDateTimestamp = $endDate_dateTime->format('Y-m-d H:i:s');


    /* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    $db->query("USE auction_site");

    $insert_query = "INSERT INTO Items (itemName, description, sellerId, categoryId, startPrice, reservePrice, endDate)
VALUES ($title, $details, $sellerId, $categoryId, $startPrice, $reservePrice, $endDateTimestamp)";

    $result = $db->query($insert_query)
        or die('Error making Create Auction query' . $mysqli->error);
    $db->close();

    // If all is successful, let user know.
    echo ('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


    ?>

</div>


<?php include_once("footer.php") ?>