<?php include_once("header.php") ?>
<?php require_once("../utils/utilities.php") ?>
<?php require_once("../database/setup.php") ?>
<?php require_once("../utils/verbose_errors.php") ?>
<?php require_once("../utils/console_log.php") ?>

<?php

// Get info from the URL:
$item_id = $_GET['item_id'];

// Fetch auction details
$query = "SELECT Items.*,
              Users.username,
              Categories.name AS category_name,
              (SELECT COUNT(*) 
                FROM Items AS SubItems 
                WHERE SubItems.sellerId = Items.sellerId
              ) AS seller_item_count,
              (SELECT AVG(rating) 
                FROM SellerRatings 
                WHERE sellerId = Items.sellerId
              ) AS seller_rating 
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
$query_bids = "SELECT bidPrice, bidderId, isHighest, isWinner, Users.username
                FROM Bids JOIN Users ON Bids.bidderId = Users.id
                WHERE itemId = ? ORDER BY bidPrice DESC";
$stmt_bids = $db->prepare($query_bids);
$stmt_bids->bind_param("i", $item_id);
$stmt_bids->execute();
$result_bids = $stmt_bids->get_result();
$total_bids = $result_bids->num_rows;
$highest_bid = $result_bids->fetch_assoc();
$current_price = $highest_bid['bidPrice'] ?? $auction['startPrice'];
$seller_rating = $auction['seller_rating']
  ? number_format($auction['seller_rating'], 1) . "/5.0"
  : 'No ratings yet';
$seller_detail = "(Auctions: {$auction['seller_item_count']}, Rating: $seller_rating)";

// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// Calculate time to auction end:
$now = new DateTime();
$end_time = new DateTime($auction['endDate']);
$time_remaining = ($now < $end_time)
  ? ' (in ' . display_time_remaining(date_diff($now, $end_time)) . ')'
  : ' (auction ended)';

// Check to see if the user has a session,

if (isset($_SESSION['userId'])) {
  $user_id = $_SESSION['userId'];
  $has_session = true;

  // Run a query to figure out if they're watching the item
  $query_follower = "SELECT userId, itemId
  FROM FollowedItems
  WHERE userId = $user_id AND itemId = $item_id";
  $follower_result = $db->query($query_follower);
  $followerRows = $follower_result->num_rows;
  if ($followerRows == 0) {
    $watching = false;
  } elseif ($followerRows == 1) {
    $watching = true;
  }
} else {
  $has_session = false;
}
?>

<div class="container my-4">
  <div class="card mx-auto" style="max-width: 1000px;">
    <div class="card-body">
      <div class="text-center">
        <img src="<?php echo htmlspecialchars($auction['imagePath']); ?>" class="img-fluid rounded mb-3" style="max-width: 500px; max-height: 400px; width: auto; height: auto;">
      </div>
      <h3 class="card-title"><?php echo htmlspecialchars($auction['itemName']); ?></h3>

      <div class="row"> <!-- Begin parallel layout -->
        <!-- Left Column: Auction Details -->
        <div class="col-md-8">
          <p class="text-muted">Description: <?php echo htmlspecialchars($auction['description']); ?></p>
          <p class="text-muted">Closes: <?php echo date('D jS M, g:ia', strtotime($auction['endDate']));
                                        echo $time_remaining; ?></p>
          <p class="text-muted">Category: <?php echo htmlspecialchars($auction['category_name']); ?></p>
          <p class="text-muted">Seller: <?php echo (
                                          '<button type="button" class="btn btn-link p-0 align-baseline" data-toggle="modal" data-target="#sellerModal">'
                                          . htmlspecialchars($auction['username'])
                                          . '</button>'
                                          . ' ' . $seller_detail); ?></p>
          <p class="text-muted">Starting bid: £<?php echo number_format($auction['startPrice'], 2); ?></p>
          <p class="text-muted">Number of bids: <?php echo number_format($total_bids); ?></p>

          <?php if (isset($_SESSION['userId']) && $_SESSION['isBuyer'] && $now < $end_time): ?>
            <div id="watch_watching" <?php if ($has_session && $watching) echo ('style="display: none"'); ?>>
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()"> + Add to watchlist</button>
            </div>
            <div id="watch_nowatch" <?php if (!$has_session || !$watching) echo ('style="display: none"'); ?>>
              <button type="button" class="btn btn-success btn-sm" disabled>Watching </button>
              <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()"> - Remove watch</button>
            </div>
          <?php endif; ?>
        </div>

        <!-- Right Column: Bidding Info -->
        <div class="col-md-4">
          <?php if ($now > $end_time): ?>
            <p>This auction ended <?php echo (date_format($end_time, 'j M H:i')); ?></p>
            <?php if (isset($_SESSION['userId']) && intval($_SESSION['userId']) == $auction['sellerId']): ?>
              <p class="text-muted"><?php echo (
                                      isset($highest_bid)
                                      ? ($highest_bid['isWinner'] ? 'Winning bid was ' : 'Highest bid was ')
                                      . number_format($current_price, 2) . ' by ' . $highest_bid['username']
                                      : 'No bids were placed.'
                                    ) ?></p>
            <?php endif; ?>
            <?php if (
              isset($_SESSION['userId'])
              && isset($highest_bid)
              && $highest_bid['isWinner']
              && $_SESSION['userId'] == $highest_bid['bidderId']
            ): ?>
              <p class="font-weight-bold text-success">You won this auction!</p>
              <form method="POST" action="rate_seller.php">
                <label for="reviewComment" class="col-form-label text-right">Rate your seller:</label>
                <div class="d-flex align-items-center">
                  <span class="mr-2">1</span>
                  <input type="range" class="form-range" id="rating" name="rating" min="1" max="5" step="1">
                  <span class="ml-2">5</span>
                </div>
                <label for="reviewComment" class="col-form-label text-right">Comments</label>
                <textarea class="form-control mb-3" id="reviewComment" rows="4" name="comment" maxlength="255"></textarea>
                <input type="hidden" name="sellerId" value="<?php echo ($auction['sellerId']); ?>">
                <input type="hidden" name="itemId" value="<?php echo ($item_id); ?>">
                <button type="submit" class="btn btn-secondary form-control">Rate / Update Rating</button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <?php if ($total_bids > 0): ?>
              <p class="text-muted mb-0">Recent bids:</p>
              <ul>
                <!-- Showing only 3 bids for space -->
                <?php for ($x = 0; $x <= 3; $x++) {
                  $bid = $result_bids->fetch_assoc();
                  if ($bid) {
                    echo ('<li class="text-muted">£' . number_format($bid['bidPrice'], 2) . '</li>');
                  } else {
                    break;
                  }
                } ?>
              </ul>
            <?php endif; ?>
            <p class="lead">Current bid: £<?php echo (number_format($current_price, 2)); ?></p>
            <?php if (isset($_SESSION['userId']) && $_SESSION['isBuyer']): ?>
              <?php if (!isset($highest_bid) || intval($_SESSION['userId']) != $highest_bid['bidderId']): ?>
                <form method="POST" action="place_bid.php">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">£</span>
                    </div>
                    <!-- Check to make sure the bid is higher than the current price -->
                    <input type="number" class="form-control" id="bid" name="bid" min="<?php echo $current_price + 1; ?>" required>
                    <!-- Carry the itemID into the form so that it can be used to make the bid -->
                    <input type="hidden" name="listingInformation" value="<?php echo $item_id ?>">
                  </div>
                  <button type="submit" class="btn btn-primary form-control">Place bid</button>
                </form>
              <?php else: ?>
                <p class="font-weight-bold text-primary">You are the highest bidder.</p>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div> <!-- End of row -->
    </div>
  </div>
</div>

<!-- Seller info modal -->
<div class="modal fade" id="sellerModal">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Seller Ratings</h4>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <?php
        $query_ratings = "SELECT rating, comment, submittedTime
                            FROM SellerRatings
                            WHERE sellerId = ? ORDER BY submittedTime DESC";
        $stmt_ratings = $db->prepare($query_ratings);
        $stmt_ratings->bind_param("i", $auction['sellerId']);
        $stmt_ratings->execute();
        $result_ratings = $stmt_ratings->get_result();
        $total_ratings = $result_ratings->num_rows;
        if ($total_ratings > 0) {
          while ($rating = $result_ratings->fetch_assoc()) {
            echo (
              '<p>Rating: '
              . number_format($rating['rating'], 1)
              . '/5.0 - Submitted: '
              . date('j M Y, g:ia', strtotime($rating['submittedTime'])) . '</p>');
            echo ('<p>Comments: ' . htmlspecialchars($rating['comment']) . '</p>');
            echo ('<hr>');
          }
        } else {
          echo ('<p>No ratings yet.</p>');
        }
        ?>
      </div>
    </div>
  </div>
  <!-- End modal -->

  <?php include_once("footer.php");
  ?>

  <script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {
      console.log("These print statements are helpful for debugging btw");

      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID, user ID, and followerRows as arguments to that function.
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'add_to_watchlist',
          argument_1: <?php echo $item_id; ?>,
          argument_2: <?php echo $user_id; ?>,
          argument_3: <?php echo $followerRows; ?>
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();
            location.reload()
          } else {
            var mydiv = document.getElementById("watch_nowatch");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of addToWatchlist func

    function removeFromWatchlist(button) {
      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID as an argument to that function.
      console.log("These print statements are helpful for debugging btw")
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'remove_from_watchlist',
          argument_1: <?php echo $item_id; ?>,
          argument_2: <?php echo $user_id; ?>,
          argument_3: <?php echo $followerRows; ?>
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();
            location.reload()
          } else {
            var mydiv = document.getElementById("watch_watching");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of RemoveFromWatchlist func
  </script>