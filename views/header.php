<?php
  session_start();
  require_once('../utils/verbose_errors.php');
  require_once('../database/setup.php');
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">
  <link rel="icon" href="../favicon.ico" type="image/x-icon">

  <title>RetroBidder</title>
</head>


<body>

  <!-- Navbars -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
    <a class="navbar-brand" href="browse.php"> RetroBidder </a>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">

        <?php
        // Displays either login or logout on the right, depending on user's
        // current status (session).
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
          echo '<div class="d-flex align-items-center">
            <span class="navbar-text text-info">' . htmlspecialchars($_SESSION['username']) . '</span>
            <span class="mx-2 text-muted">|</span>
            <a class="nav-link p-0 text-danger" href="logout.php">Logout</a>
          </div>';
        } else {
          echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
        }
        ?>

      </li>
    </ul>
  </nav>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <ul class="navbar-nav align-middle">
      <li class="nav-item mx-1">
        <a class="nav-link" href="browse.php">Browse</a>
      </li>
      <?php
      if (isset($_SESSION['isBuyer']) && $_SESSION['isBuyer']) {
        echo ('
          <li class="nav-item mx-1">
            <a class="nav-link" href="mybids.php">My Bids</a>
          </li>
          <li class="nav-item mx-1">
            <a class="nav-link" href="recommendations.php">Recommended</a>
          </li>');
        }
      if (isset($_SESSION['isSeller']) && $_SESSION['isSeller']) {
        echo('
          <li class="nav-item mx-1">
            <a class="nav-link" href="mylistings.php">My Listings</a>
          </li>
          <li class="nav-item ml-3">
            <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
          </li>');
      }
      echo '</ul>';
      if (isset($_SESSION['userId'])) {
        // Get unread notifications
        $query_result = $db->query(
          "SELECT COUNT(*) as unread FROM Notifications WHERE userId = " . $_SESSION['userId'] . " AND isRead = 0"
        );
        $unread_count = $query_result->fetch_assoc();
        $unread_badge = (
          $unread_count['unread'] > 0 
          ? '<span class="badge badge-pill badge-primary mx-1">'. $unread_count['unread'] . '</span>'
          : ''
        );
        echo('
          <ul class="navbar-nav ml-auto">
            <li class="nav-item ml-3">
              <a class="nav-link" href="notifications.php">' .
              'Notifications'
              . $unread_badge
              . '</a>
            </li>
          </ul>');
      }
      ?>
  </nav>

  <!-- Login modal -->
  <div class="modal fade" id="loginModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Login</h4>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <form method="POST" action="login_result.php">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" placeholder="Username" name="username">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" placeholder="Password" name="password">
            </div>
            <button type="submit" class="btn btn-primary form-control">Sign in</button>
          </form>
          <div class="text-center">or <a href="register.php">create an account</a></div>
        </div>
      </div>
    </div>
  </div>
  <!-- End modal -->

</body> 
