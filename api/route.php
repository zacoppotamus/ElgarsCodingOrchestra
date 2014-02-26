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

// Create an endpoint to create a new dataset.
route::add(route::POST, "/datasets", function() {
    $data->name = isset($_POST['name']) ? strtolower(trim($_POST['name'])) : null;
    $data->description = isset($_POST['description']) ? trim($_POST['description']) : null;

    include("datasets/create.php");
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
    $data->offset = isset($_GET['offset']) && intval($_GET['offset']) >= 0 ? intval($_GET['offset']) : 0;
    $data->limit = isset($_GET['limit']) && intval($_GET['limit']) >= 1 ? intval($_GET['limit']) : null;
    $data->sort = isset($_GET['sort']) ? json_decode($_GET['sort'], true) : null;
    $data->fields = isset($_GET['fields']) ? json_decode($_GET['fields'], true) : null;
    $data->exclude = isset($_GET['exclude']) ? json_decode($_GET['exclude'], true) : null;

    include("datasets/data.php");
});

// Create an endpoint to insert new data into the dataset.
route::add(route::POST, "/datasets/(\w+)\.(\w+)/data", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->row = isset($_POST['row']) ? json_decode($_POST['row'], true) : null;
    $data->rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;

    include("datasets/data/insert.php");
});

// Create an endpoint to update data in the dataset.
route::add(route::PUT, "/datasets/(\w+)\.(\w+)/data", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->query = isset($_POST['query']) ? json_decode($_POST['query'], true) : null;
    $data->changes = isset($_POST['changes']) ? json_decode($_POST['changes'], true) : null;

    include("datasets/data/update.php");
});

// Create an endpoint to delete data from the dataset.
route::add(route::DELETE, "/datasets/(\w+)\.(\w+)/data", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->query = isset($_POST['query']) ? json_decode($_POST['query'], true) : null;

    include("datasets/data/delete.php");
});

// Create an endpoint to list the indexes on a dataset.
route::add(route::GET, "/datasets/(\w+)\.(\w+)/indexes", function($prefix, $name) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/indexes.php");
});

/*route::add("/datasets/:dataset/calc/polyfit", "datasets/calc/polyfit.php");
route::add("/datasets/:dataset/calc/stats", "datasets/calc/stats.php");

// Add endpoints for the tests.
route::add("/tests/run", "tests/run.php");
route::add("/tests/import", "tests/import.php");*/

/*!
 * Perform the routing request.
 */

route::parse();

include("main.php");
exit;

?>
