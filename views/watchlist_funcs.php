 <?php
  require_once "mailer.php";
  // Just a general note: I think I might have to move the second half of this code. This page gets called in listings.php, 
  // which I think means that it all runs there, which isn't consistent with me defining events etc. 

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
    $res = "success";
  } else if ($_POST['functionname'] == "remove_from_watchlist") {
    $query = "DELETE FROM FollowedItems ($user_id, $item_id)";
    $db->query($query);
    $res = "success";
  }
  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  echo $res;

  // Events 

  // Event: Auction closing 
  // TODO: define the condition that has to be met here


  // Event: 
  //TODO: I need to check that what I have in mind here is consistent with what David has already done
  // Set out the body and heading (strings) for each of the events that we've defined: Closing an auction (Winner and not-winner) and Being outbid 

  // Define the functions that we'll use to send notificaitons 

  //TODO: Define a send email protocol (to be called later)
  //TODO: Define a protocol for storing a notification in the notificaiotns table

  // Sending notificaitons
  // Create a function (triggered elsewhere) which sends both an email and notification in the event of an auction closing 

  // Event: Auction closing
  // SQL query that checks whether the userId is not on the winning bid, and then whether it is on the winning bid
  // Sends inserts the relevant variable into the email    

  // Event: Outbid by someone else 
  // Triggers elsewhere, but if this function is triggered: inserts the relevant text into a notification, and sends them.


  // N.B. I'm not sure of the ordering between Notification functions and sending notifications. 
  // It might be better to use sending notifications to outline a set of variables, which then get inserted into the function... Eh, not a big issue rn. 

  ?>