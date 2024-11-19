<?php include_once("header.php") ?>
<?php require_once("../database/setup.php") ?>

<div class="container">

  <h2 class="my-3">Your Notifications</h2>

  <ul class="list-group">
    <?php
    $userId = $_SESSION['userId'];
    $notifications_query = "SELECT * FROM Notifications WHERE userId = $userId ORDER BY id DESC";
    $notifications_result = $db->query($notifications_query);
    if ($notifications_result->num_rows > 0) {
      while ($row = $notifications_result->fetch_assoc()) {
        echo '<li class="list-group-item">';
        echo '<h5>' . $row['subject'] . '</h5>';
        echo '<p class="mb-0">' . $row['message'] . '</p>';
        echo '</li>';
      }
    } else {
      echo '<li class="list-group-item">No notifications</li>';
    }
    ?>
  </ul>

</div>

<?php $db->close(); ?>
<?php include_once("footer.php") ?>