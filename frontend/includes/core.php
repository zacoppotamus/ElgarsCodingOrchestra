<?php

require_once "rainhawk.php";

// Start session handling.
session_start();

// Check for a login submission.
if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

// Get the user's information.
$mashape_key = isset($_SESSION['apiKey']) ? $_SESSION['apiKey'] : null;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Set up the Rainhawk wrapper.
$rainhawk = new Rainhawk($mashape_key);

?>