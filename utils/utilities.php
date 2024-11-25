<?php

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval)
{

  if ($interval->days == 0 && $interval->h == 0) {
    // Less than one hour remaining: print mins + seconds:
    $time_remaining = $interval->format('%im %Ss');
  } else if ($interval->days == 0) {
    // Less than one day remaining: print hrs + mins:
    $time_remaining = $interval->format('%hh %im');
  } else {
    // At least one day remaining: print days + hrs:
    $time_remaining = $interval->format('%ad %hh');
  }

  return $time_remaining;
}

// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  } else {
    $desc_shortened = $desc;
  }

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Print HTML
  echo ('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}

// Populate Categories for Form
// If a category has already been selected, pass the id as $selected
// If you want the option to select all categories, pass true as $all - currently this will only show if a category has been selected
// Pass in database connection as $db
function categories_form($db, $placeholder, $selected, $all)
{

  $db->query("USE auction_site");
  $query = "SELECT id, name FROM Categories";
  $result = mysqli_query($db, $query);

  // If a category has already been selected, it will stay selected on page reload
  if (is_null($selected)) {
    echo "<option selected disabled>$placeholder</option>";
    while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
      $name = $row['name'];
      echo "<option value='$id'>$name</option>";
    }
  } else {
    echo $all ? "<option value=''>All</option>" : "<option disabled>$placeholder</option>";
    while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
      $name = $row['name'];
      if ($id == $selected) {
        echo "<option value='$id' selected>$name</option>";
      } else {
        echo "<option value='$id'>$name</option>";
      }
    }
  }
}

// Find current highest bid for an item
// Pass in database connection as db
function highest_bid($db, $item_id)
{
  $db->query("USE auction_site");
  $query = "SELECT MAX(bidPrice) AS highestBid FROM Bids WHERE itemId = $item_id";
  $result = mysqli_query($db, $query);

  $row = mysqli_fetch_array($result);
  $bid = $row['highestBid'];

  return $bid;
}

// Find the number of bids for an item
function number_of_bids($db, $item_id)
{

  $db->query("USE auction_site");
  $query = "SELECT COUNT(*) AS numberOfBids FROM Bids WHERE itemId = $item_id";
  $result = mysqli_query($db, $query);

  $row = mysqli_fetch_array($result);
  $bids_number = $row['numberOfBids'];

  return $bids_number;
}

// Build a listings query - for browse page, mybids, and mylistings
function construct_listings_query($keyword, $category, $ordering, $bidder_id, $seller_id, $active_only)
{

  $query = "SELECT id, itemName, description, endDate, GREATEST(startPrice, IFNULL(MAX(bidPrice), startPrice)) AS currentPrice 
FROM Items LEFT JOIN Bids ON Items.id = Bids.itemId ";

  // Add clause for bidder - used on mybids page
  if (!is_null($bidder_id)) {
    $query .= "WHERE Bids.bidderId = $bidder_id ";
  }

  // Add clause for seller - used on mylistings page
  if (!is_null($seller_id)) {
    $query .= "WHERE Items.sellerId = $seller_id ";
  }

  // Add keyword search clause if defined - searches itemName and description
  if (!is_null($keyword)) {
    $query .= "WHERE itemName LIKE '%$keyword%' OR description LIKE '%$keyword%' ";
  }

  // Add category clause if defined
  if (!is_null($category)) {
    if (!is_null($keyword)) {
      $query .= "AND categoryId = $category ";
    } else {
      $query .= "WHERE categoryId = $category ";
    }
  }

  if ($active_only) {
    if (!is_null($category) || !is_null($keyword)) {
      $query .= "AND endDate > CURRENT_TIMESTAMP ";
    } else {
      $query .= "WHERE endDate > CURRENT_TIMESTAMP ";
    }
  }

  // Add order by clause - default sort = endDate
  $query .= "GROUP BY id ORDER BY $ordering";

  return $query;
}

// Uses a while loop to print a list item for each auction listing
//  retrieved from the query
function listings_loop($db, $query_result)
{
  echo '<ul class="list-group">';

  while ($row = mysqli_fetch_array($query_result)) {
    $item_id = $row['id'];
    $item_name = $row['itemName'];
    $description = $row['description'];
    $current_price = $row['currentPrice'];
    $num_bids = number_of_bids($db, $item_id);
    $end_date = new DateTime($row['endDate']);
    print_listing_li($item_id, $item_name, $description, $current_price, $num_bids, $end_date);
  }

  echo '</ul>';
}

?>