<?php

// Show errors all the time.
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

// Set the header content type including charset.
include("includes/classes/route.class.php");
header("content-type: application/json; charset=utf8");
header("access-control-allow-origin: *");

/*!
 * Add all of the possible routes to the class, including
 * API paths and stuff.
 */

// Add main endpoints for primary methods.
route::add("/", "main.php");
route::add("/datasets", "datasets.php");
route::add("/delete", "delete.php");
route::add("/index", "index.php");
route::add("/insert", "insert.php");
route::add("/ping", "ping.php");
route::add("/select", "select.php");
route::add("/update", "update.php");

// Add endpoints for calculations.
route::add("/calc/polyfit", "calc/polyfit.php");
route::add("/calc/stats", "calc/stats.php");

// Add endpoints for the tests.
route::add("/tests/run", "tests/run.php");
route::add("/tests/import", "tests/import.php");

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