<?php include_once("header.php") ?>
<?php require("../utils/utilities.php") ?>
<?php require_once("../database/setup.php") ?>

<?php
// If user is not logged in or not a seller, they should not be able to
// use this page.
if (!$_SESSION['loggedIn'] || !$_SESSION['isSeller']) {
  header('Location: browse.php');
}
?>

<div class="container">

  <!-- Create auction form -->
  <div style="max-width: 800px; margin: 10px auto">
    <h2 class="my-3">Create new auction</h2>
    <div class="card">
      <div class="card-body">
        <!-- Note: This form does not do any dynamic / client-side / 
      JavaScript-based validation of data. It only performs checking after 
      the form has been submitted, and only allows users to try once. You 
      can make this fancier using JavaScript to alert users of invalid data
      before they try to send it, but that kind of functionality should be
      extremely low-priority / only done after all database functions are
      complete. -->
        <form method="post" action="create_auction_result.php" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Item Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" placeholder="e.g. GoldenEye 007" required>
              <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling, which will display in listings.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionDescription" class="col-sm-2 col-form-label text-right">Description</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="auctionDescription" rows="4" name="auctionDescription" maxlength="255" required></textarea>
              <small id="detailsHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Full details of the listing to help bidders decide if it's what they're looking for.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
            <div class="col-sm-10">
              <select class="form-control" id="auctionCategory" name="auctionCategory" required>
                <?php
                categories_form($db, '-', null, false);
                ?>
              </select>
              <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionImage" class="col-sm-2 col-form-label text-right">Item Image</label>
            <div class="col-sm-10">
              <input type="file" class="form-control-file" id="auctionImage" name="auctionImage" accept="image/*" onchange="handleFileSelect()">
              <small id="imageHelp" class="form-text text-muted">Optional. Upload a <b>single</b> (max 2 MB) image of the item. Supported formats are jpg, jpeg, png, or webp. </small>
              <div id="fileNameContainer" style="margin-top: 10px;">
                <span id="fileName" style="display:none;"></span>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" step="0.01" class="form-control" id="auctionStartPrice" name="auctionStartPrice" required>
              </div>
              <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" step="0.01" class="form-control" id="auctionReservePrice" name="auctionReservePrice">
              </div>
              <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
            <div class="col-sm-10">
              <input type="datetime-local" class="form-control" id="auctionStartDate" name="auctionStartDate">
              <small id="startDateHelp" class="form-text text-muted">Optional. Select when you want the auction to start - if left blank it will start immediately.</small>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
            <div class="col-sm-10">
              <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate" required>
              <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
            </div>
          </div>
          <button type="submit" class="btn btn-primary form-control">Create Auction</button>
        </form>
      </div>
    </div>
  </div>

</div>


<script>
  // Function to make upload button and text invisible after upload
  function handleFileSelect() {
    const fileInput = document.getElementById("auctionImage");
    const fileName = document.getElementById("fileName");
    const optText = document.getElementById("imageHelp");
    const file = fileInput.files[0];

    if (file) {
      optText.style.display = 'none';
      fileInput.style.display = 'none';
      fileName.style.display = 'inline';
      fileName.textContent = `${file.name}`;
    }
  }
</script>

<?php $db->close() ?>
<?php include_once("footer.php") ?>