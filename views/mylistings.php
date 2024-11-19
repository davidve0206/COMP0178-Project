<?php include_once("header.php") ?>
<?php require("../utils/utilities.php") ?>
<?php require_once("../database/setup.php") ?>

<div class="container">

  <h2 class="my-3">My listings</h2>

  <?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // Check user's credentials (cookie/session).
  if (!isset($_SESSION['userId'])) {
    echo 'You must be logged in to view bids.';
  } else {
    $user_id = intval($_SESSION['userId']);

    if ($user_id == 0) {
      echo 'Invalid user ID.';
    }
  }

  // Perform a query to pull up their auctions.
  $db->query("USE auction_site");

  $query = construct_listings_query(null, null, "endDate", null, $user_id);
  $result = mysqli_query($db, $query);

  // Loop through results and print them out as list items.
  listings_loop($db, $result);

  ?>

  <?php include_once("footer.php") ?>