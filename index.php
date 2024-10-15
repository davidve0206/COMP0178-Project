<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  // For now, index.php just redirects to browse.php, but you can change this
  // if you like.
  require "database/setup.php";
  header("Location: views/browse.php");
?>