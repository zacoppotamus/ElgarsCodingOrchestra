<?php

require_once "rainhawk.php";
require_once "redis.php";

// Start session handling.
session_start();

// Get the user's information.
$mashape_key = isset($_SESSION['apiKey']) ? $_SESSION['apiKey'] : null;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Set up the Rainhawk wrapper.
$rainhawk = new Rainhawk($mashape_key);

// Connect to our Redis cache.
redis::connect("127.0.0.1", 6379);

?>