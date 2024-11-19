 <?php

  // Follow or unfollow an item ()
  if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }

  // Extract arguments from the POST variables:
  $item_id = $_POST['arguments'];

  if (!isset($_SESSION['userId'])) {
    $error = 'You must be logged in to add an item to your watchlist.';
    echo  "<p><span class='font-weight-bold'>Error: </span> $error</p>";
  } else {
    $user_id = intval($_SESSION['userId']);
    if ($user_id == 0) {
      $error = 'Invalid userId.';
      echo  "<p><span class='font-weight-bold'>Error: </span> $error</p>";
    }
  }
  // Preparing to query the database 
  // Question: do I need this? I'd like to understand why we're being explicit about the database we're connecting to
  $db->query("USE auction_site");

  // Update the database (add or remove an item to the watchlist) and return success/failure
  if ($_POST['functionname'] == "add_to_watchlist") {
    $query = "INSERT INTO FollowedItems ($user_id, $item_id)";
    $db->query($query);
    $res = "You have successfully been added to the watchlist";
  } else if ($_POST['functionname'] == "remove_from_watchlist") {
    $query = "DELETE FROM FollowedItems ($user_id, $item_id)";
    $db->query($query);
    $res = "You have successfully been removed from the watchlist";
  }
  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  echo $res;

  ?>