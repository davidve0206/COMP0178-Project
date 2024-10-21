<?php
  // This allow verbose errors
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  
  // Require the db setup every time we need to refer to it
  require_once "database/setup.php";
  
  // Comment this out if you dont want to have a fresh database
  require_once "database/migrations.php";
  migate($db);

  // For now, index.php just redirects to browse.php, but you can change this
  // if you like.
  header("Location: views/browse.php");
?>