<?php include_once("header.php") ?>
<?php require("utilities.php") ?>

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
              <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything">
            </div>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-group">
            <label for="cat" class="sr-only">Search within:</label>
            <select class="form-control" id="cat">
              <?php
              categories_form("Category: ");
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-inline">
            <label class="mx-2" for="order_by">Sort by:</label>
            <select class="form-control" id="order_by">
              <option selected value="pricelow">Price (low to high)</option>
              <option value="pricehigh">Price (high to low)</option>
              <option value="date">Soonest expiry</option>
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
// Retrieve these from the URL
if (!isset($_GET['keyword'])) {
  // TODO: Define behavior if a keyword has not been specified.
} else {
  $keyword = $_GET['keyword'];
}

if (!isset($_GET['cat'])) {
  // TODO: Define behavior if a category has not been specified.
} else {
  $category = $_GET['cat'];
}

if (!isset($_GET['order_by'])) {
  // TODO: Define behavior if an order_by value has not been specified.
} else {
  $ordering = $_GET['order_by'];
}

if (!isset($_GET['page'])) {
  $curr_page = 1;
} else {
  $curr_page = $_GET['page'];
}

/* TODO: Use above values to construct a query. Use this query to 
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */

/* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
$num_results = 96; // TODO: Calculate me for real
$results_per_page = 10;
$max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

  <!-- TODO: If result set is empty, print an informative message. Otherwise... -->

  <ul class="list-group">

    <!-- Using a while loop to print a list item for each auction listing
     retrieved from the query -->

    <!-- Initial implementation - needs to be reconfigured to work with search queries -->
    <?php
    require "../database/setup.php";

    $db->query("USE auction_site");
    $query = "SELECT id, itemName, description, startPrice, endDate FROM Items";
    $result = mysqli_query($db, $query)
      or die('Error fetching item information' . $db->error);

    while ($row = mysqli_fetch_array($result)) {
      $item_id = $row['id'];
      $item_name = $row['itemName'];
      $description = $row['description'];
      $startPrice = $row['startPrice'];
      $current_price = max($startPrice, floatval(highest_bid($item_id)));
      $num_bids = number_of_bids($item_id);
      $end_date = new DateTime($row['endDate']);
      print_listing_li($item_id, $item_name, $description, $current_price, $num_bids, $end_date);
    }
    
    $db->close();
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



<?php include_once("footer.php") ?>