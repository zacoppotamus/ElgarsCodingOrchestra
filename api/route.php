<?php

// Show errors all the time.
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

// Set the header content type including charset.
include("includes/classes/route.class.php");
header("content-type: application/json; charset=utf8");

/*!
 * Add all of the possible routes to the class, including
 * API paths and stuff.
 */

route::add("/", "index.php");
route::add("/select", "select.php");
route::add("/insert", "insert.php");
route::add("/update", "update.php");

/*!
 * Perform the routing request.
 */

route::$uri = (isset($_GET['uri']) && !empty($_GET['uri'])) ? trim($_GET['uri']) : null;

if(route::parse()) {
    include(route::$file);
    exit;
} else {
    include("index.php");
    exit;
}

?>