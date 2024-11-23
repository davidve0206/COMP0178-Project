 <?php
  require_once("../database/setup.php");

  // Follow or unfollow an item ()
  if (!isset($_POST['functionname']) || !isset($_POST['argument_1']) || !isset($_POST['argument_2']) || !isset($_POST['argument_3'])) {
    return;
  }
  // Extract variables from the POST variables (follower_rows exists to check that the user isn't already on the watchlist)
  $item_id = $_POST['argument_1'];
  $user_id = $_POST['argument_2'];
  $follower_rows = $_POST['argument_3'];

  // Use argument to update the database (add or remove an item to the watchlist), returning success (if successful)
  if ($_POST['functionname'] == 'add_to_watchlist') {
    if ($follower_rows < 1) {
      $query = "INSERT INTO FollowedItems (userId, itemId) VALUES ('$user_id', '$item_id')";
      $db->query($query);
    }
    $res = "success";
  } elseif ($_POST['functionname'] == 'remove_from_watchlist') {
    $query = "DELETE FROM FollowedItems WHERE userId = '$user_id' AND itemId = '$item_id'";
    $db->query($query);
    $res = "success";
  }
  //  Note: Echoing from this PHP function will return the value as a string.
  //  If multiple echo's in this file exist, they will concatenate together,
  //  so be careful. You can also return JSON objects (in string form) using
  //  echo json_encode($res).
  echo $res;

  ?>