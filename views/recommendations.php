<?php include_once("header.php") ?>
<?php require("../utils/utilities.php") ?>
<?php require_once("../database/setup.php") ?>

<div class="container">

  <h2 class="my-3">Recommendations for you</h2>

  <?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // Check user's credentials (cookie/session).
  if (!isset($_SESSION['userId'])) {
    echo 'You must be logged in to get recommendations.';
  } else {
    $user_id = intval($_SESSION['userId']);

    if ($user_id == 0) {
      echo 'Invalid user ID.';
    }
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  } else {
    $curr_page = $_GET['page'];
  }

  // Perform a query to pull up auctions they might be interested in.
  $db->query("USE auction_site");
  $query = "SELECT id, itemName, description, startDate, endDate, GREATEST(startPrice, IFNULL(MAX(bidPrice), startPrice)) AS currentPrice
            FROM Items
            LEFT JOIN Bids ON Items.id = Bids.itemId
            WHERE endDate > NOW()
            AND startDate < NOW()
            AND Items.categoryId IN (
              SELECT DISTINCT(categoryId)
              FROM Items JOIN Bids ON Items.id = Bids.itemId
              WHERE bidderId IN (
                SELECT DISTINCT(bidderId)
                FROM Bids
                WHERE itemId IN (
                  SELECT DISTINCT(itemId)
                  FROM Bids
                  WHERE bidderId = $user_id
                )
              AND bidderId <> $user_id
              )
            )
            AND Items.id NOT IN (
              SELECT DISTINCT(itemId)
              FROM Bids
              WHERE bidderId = $user_id
            )
            GROUP BY Items.id";

  $result = mysqli_query($db, $query);

  $num_results = $result->num_rows;
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
  $offset = 10 * ($curr_page - 1);

  // Limit results by page
  $limit_query = $query . " LIMIT 10 OFFSET $offset;";

  $limit_result = mysqli_query($db, $limit_query);
  ?>

  <div class="container mt-5">

    <!-- If result set is empty, print an informative message -->

    <?php
    if ($num_results == 0) {

      echo '<p> No recommendations found! You must bid on an item before we can give you recommendations. </p>';
    } else {
      listings_loop($db, $limit_result);
    }
    ?>

    <?php include_once('../utils/pagination.php') ?>


  </div>
</div>