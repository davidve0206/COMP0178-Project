<?php
require_once "utils/verbose_errors.php";

// Comment this out if you dont want to have a fresh database
require_once "database/migrations.php";
migrate();

// For now, index.php just redirects to browse.php, but you can change this
// if you like.
header("Location: views/browse.php");
