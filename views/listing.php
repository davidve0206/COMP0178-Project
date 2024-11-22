<?php include_once("header.php")?>
<?php require("../utils/utilities.php")?>
<?php require("../database/setup.php")?>

<?php
  // useful for debugging
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
  // Get info from the URL:
  $item_id = $_GET['item_id'];

  // Fetch auction details
  $query = "SELECT Items.*, Users.username, Categories.name AS category_name 
              FROM Items 
              JOIN Users ON Items.sellerId = Users.id 
              JOIN Categories ON Items.categoryId = Categories.id 
              WHERE Items.id = ?";
  $stmt = $db->prepare($query);
  $stmt->bind_param("i", $item_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $auction = $result->fetch_assoc();

  // Fetch current bid
  $query_bids = "SELECT MAX(bidPrice) AS current_bid FROM Bids WHERE itemId = ?";
  $stmt_bids = $db->prepare($query_bids);
  $stmt_bids->bind_param("i", $item_id);
  $stmt_bids->execute();
  $result_bids = $stmt_bids->get_result();
  $bid = $result_bids->fetch_assoc();
  $current_price = $bid['current_bid'] ?? $auction['startPrice'];
  $title = $auction['itemName'];
  $description = $auction['description'];

  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  
  // Calculate time to auction end:
  $now = new DateTime();
  $end_time = new DateTime($auction['endDate']);
  $time_remaining = ($now < $end_time)
    ? ' (in ' . display_time_remaining(date_diff($now, $end_time)) . ')'
    : ' (auction ended)';

  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  $has_session = true;
  $watching = false;
?>

<div class="container my-4">
  <div class="card mx-auto" style="max-width: 1000px;">
    <div class="card-body">
      <div class="text-center">
        <?php if (!empty($auction['imagePath'])): ?>
          <img src="<?php echo htmlspecialchars($auction['imagePath']); ?>" class="img-fluid rounded mb-3" style="max-width: 500px; max-height: 400px; width: auto; height: auto;">
        <?php endif; ?>
    </div>
      <h3 class="card-title"><?php echo htmlspecialchars($auction['itemName']); ?></h3>

      <div class="row"> <!-- Begin parallel layout -->
        <!-- Left Column: Auction Details -->
        <div class="col-md-8">
          <p class="text-muted">Description: <?php echo htmlspecialchars($auction['description']); ?></p>
          <p class="text-muted">Closes: <?php echo date('D jS M, g:ia', strtotime($auction['endDate'])); echo $time_remaining; ?></p>
          <p class="text-muted">Category: <?php echo htmlspecialchars($auction['category_name']); ?></p>
          <p class="text-muted">Starting bid: £<?php echo number_format($auction['startPrice'], 2); ?></p>

          <?php if ($now < $end_time): ?>
            <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
            </div>
            <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
              <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
              <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
            </div>
          <?php endif; ?>
        </div>

        <!-- Right Column: Bidding Info -->
        <div class="col-md-4">
          <?php if ($now > $end_time): ?>
            <p>This auction ended <?php echo(date_format($end_time, 'j M H:i')); ?></p>
          <?php else: ?>
            <p class="lead">Current bid: £<?php echo(number_format($current_price, 2)); ?></p>
            <form method="POST" action="place_bid.php">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <!-- Check to make sure the bid is higher than the current price -->
                <input type="number" class="form-control" id="bid" name="bid" min="<?php echo $current_price + 1; ?>" required>
              </div>
              <button type="submit" class="btn btn-primary form-control">Place bid</button>
            </form>
          <?php endif; ?>
        </div>
      </div> <!-- End of row -->
    </div>
  </div>
</div>



<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func
</script>