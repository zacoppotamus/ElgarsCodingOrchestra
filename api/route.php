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
route::add("/ping", "ping.php");

// Add endpoints for /datasets/.
route::add("/datasets", "datasets/main.php");
route::add("/datasets/:dataset/create", "datasets/create.php");
route::add("/datasets/:dataset/delete", "datasets/delete.php");
route::add("/datasets/:dataset/index", "datasets/index.php");
route::add("/datasets/:dataset/insert", "datasets/insert.php");
route::add("/datasets/:dataset/select", "datasets/select.php");
route::add("/datasets/:dataset/update", "datasets/update.php");
route::add("/datasets/:dataset/calc/polyfit", "datasets/calc/polyfit.php");
route::add("/datasets/:dataset/calc/stats", "datasets/calc/stats.php");

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
    include("main.php");
    exit;
}

?>
