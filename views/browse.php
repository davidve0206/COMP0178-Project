<?php include_once("header.php") ?>
<?php require("../utils/utilities.php") ?>
<?php require_once("../database/setup.php") ?>
<?php require_once("../utils/close_auctions.php") ?>

<?php
// Retrieve search parameters from the URL - defined at top so that they can be used as values in the search fields to persist inputs
if (!isset($_GET['keyword']) or trim($_GET['keyword']) == '') {
  $keyword = null;
} else {
  $keyword = $_GET['keyword'];
}

if (!isset($_GET['category']) or $_GET['category'] == '') {
  $category = null;
} else {
  $category = $_GET['category'];
}

if (!isset($_GET['order_by'])) {
  $ordering = 'endDate';
} else {
  $ordering = $_GET['order_by'];
}

if (!isset($_GET['page'])) {
  $curr_page = 1;
} else {
  $curr_page = $_GET['page'];
}
?>

<div class="container">

  <h2 class="my-3">Browse listings</h2>

  <div id="searchSpecs">
    <!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
    <form method="get" action="browse.php">
      <div class="row">
        <div class="col-md-5 pr-0">
          <div class="form-group">
            <label for="keyword" class="sr-only">Search keyword:</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent pr-0 text-muted">
                  <i class="fa fa-search"></i>
                </span>
              </div>
              <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything" name="keyword"
                value=<?php echo $keyword ?>>
            </div>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-group">
            <label for="category" class="sr-only">Search within:</label>
            <select class="form-control" id="category" name="category">
              <?php
              categories_form($db, "Category: ", $category, true);
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-inline">
            <label class="mx-2" for="order_by">Sort by:</label>
            <select class="form-control" id="order_by" name="order_by">
              <?php
              // This makes sure that the selected value stays in the form after the page reload
              $sorting_values = ["endDate" => "Ending soonest", "currentPrice" => "Price (low to high)", "currentPrice DESC" => "Price (high to low)"];

              foreach ($sorting_values as $value => $name) {
                if ($value == $ordering) {
                  echo "<option value='$value' selected>$name</option>";
                } else {
                  echo "<option value='$value'>$name</option>";
                }
              }
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-1 px-0">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>
  </div> <!-- end search specs bar -->


</div>

<?php

/* Construct SQL query, using search parameters if set. */

$db->query("USE auction_site");

$query = "SELECT id, itemName, description, endDate, GREATEST(startPrice, IFNULL(MAX(bidPrice), startPrice)) AS currentPrice 
FROM Items LEFT JOIN Bids ON Items.id = Bids.itemId ";

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

// Add order by clause - default sort = endDate
$query .= "GROUP BY id ORDER BY $ordering;";

$result = mysqli_query($db, $query);

/* Working out total number of results that satisfy the above query so that pages can be displayed correctly */
$num_results = $result->num_rows;
$results_per_page = 10;
$max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

  <!-- TODO: If result set is empty, print an informative message. Otherwise... -->

  <ul class="list-group">

    <!-- Using a while loop to print a list item for each auction listing
     retrieved from the query -->
    <?php

    while ($row = mysqli_fetch_array($result)) {
      $item_id = $row['id'];
      $item_name = $row['itemName'];
      $description = $row['description'];
      $current_price = $row['currentPrice'];
      $num_bids = number_of_bids($db, $item_id);
      $end_date = new DateTime($row['endDate']);
      print_listing_li($item_id, $item_name, $description, $current_price, $num_bids, $end_date);
    }
    ?>

  </ul>

  <!-- Pagination for results listings -->
  <nav aria-label="Search results pages" class="mt-5">
    <ul class="pagination justify-content-center">

      <?php

      // Copy any currently-set GET variables to the URL.
      $querystring = "";
      foreach ($_GET as $key => $value) {
        if ($key != "page") {
          $querystring .= "$key=$value&amp;";
        }
      }

      $high_page_boost = max(3 - $curr_page, 0);
      $low_page_boost = max(2 - ($max_page - $curr_page), 0);
      $low_page = max(1, $curr_page - 2 - $low_page_boost);
      $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

      if ($curr_page != 1) {
        echo ('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
      }

      for ($i = $low_page; $i <= $high_page; $i++) {
        if ($i == $curr_page) {
          // Highlight the link
          echo ('
    <li class="page-item active">');
        } else {
          // Non-highlighted link
          echo ('
    <li class="page-item">');
        }

        // Do this in any case
        echo ('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
      }

      if ($curr_page != $max_page) {
        echo ('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
      }
      ?>

    </ul>
  </nav>


</div>

<?php $db->close(); ?>
<?php include_once("footer.php") ?>