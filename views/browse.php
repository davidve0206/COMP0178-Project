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

$query = construct_listings_query($keyword, $category, $ordering, null, null);

$result = mysqli_query($db, $query);

/* Working out total number of results that satisfy the above query so that pages can be displayed correctly */
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

    echo '<p> No results found. </p>';
  } else {
    listings_loop($db, $limit_result);
  }
  ?>

  <?php include_once('../utils/pagination.php') ?>


</div>

<?php $db->close(); ?>
<?php include_once("footer.php") ?>