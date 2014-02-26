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

// Create an object to store our parameters.
$data = (object)array();

// Create a main endpoint.
route::add(route::GET, "/", function() {
    include("main.php");
});

// Create an endpoint for the ping command.
route::add(route::GET, "/ping", function() {
    include("ping.php");
});

// Create an endpoint to list all available datasets.
route::add(route::GET, "/datasets", function() {
    include("datasets.php");
});

// Create an endpoint to get the info about a dataset.
route::add(route::GET, "/datasets/(\w+)\.(\w+)", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/info.php");
});

// Create an endpoint to perform a query on a dataset.
route::add(route::GET, "/datasets/(\w+)\.(\w+)/data", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->query = isset($_GET['query']) ? json_decode($_GET['query'], true) : null;

    include("datasets/data/select.php");
});

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

route::$uri = isset($_GET['uri']) && !empty($_GET['uri']) ? trim($_GET['uri']) : null;

if(route::parse()) {
    include(route::$file);
    exit;
} else {
    include("main.php");
    exit;
}

?>
