 <?php
  require_once("../database/setup.php");

  // Follow or unfollow an item ()
  if (!isset($_POST['functionname']) || !isset($_POST['itemId']) || !isset($_POST['userId']) || !isset($_POST['followerRows'])) {
    return;
  }
  // Extract variables from the POST variables (follower_rows exists to check that the user isn't already on the watchlist)
  $item_id = $_POST['itemId'];
  $user_id = $_POST['userId'];
  $follower_rows = $_POST['followerRows'];

  // Use argument to update the database (add or remove an item to the watchlist), returning success (if successful)
  if ($_POST['functionname'] == 'add_to_watchlist') {
    if ($follower_rows < 1) {
      $query = "INSERT INTO FollowedItems (userId, itemId) VALUES ('$user_id', '$item_id')";
      $db->query($query);
    }
    $res = "Item successfully added to the watchlist";
  } elseif ($_POST['functionname'] == 'remove_from_watchlist') {
    $query = "DELETE FROM FollowedItems WHERE userId = '$user_id' AND itemId = '$item_id'";
    $db->query($query);
    $res = "Item successfully removed from the watchlist";
  }
  //  Note: Echoing from this PHP function will return the value as a string.
  //  If multiple echo's in this file exist, they will concatenate together,
  //  so be careful. You can also return JSON objects (in string form) using
  //  echo json_encode($res).
  echo $res;

  ?>