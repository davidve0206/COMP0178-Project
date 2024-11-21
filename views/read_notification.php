<?php
require_once "../database/setup.php";

if (isset($_POST['notificationId'])) {
    $notification_id = $_POST['notificationId'];

    $query = "UPDATE Notifications SET isRead = 1 WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
}

header("refresh:0;");