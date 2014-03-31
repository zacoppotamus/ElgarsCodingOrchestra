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

// Set the data object.
$data = new stdClass;

// Create a main endpoint.
route::get("/", function() use($data) {
    include("main.php");
});

// Create an endpoint for the ping command.
route::get("/ping", function() use($data) {
    include("ping.php");
});

// Create an endpoint to list all available datasets.
route::get("/datasets", function() use($data) {
    include("datasets.php");
});

// Create an endpoint to create a new dataset.
route::post("/datasets", function() use($data) {
    $data->name = isset($_POST['name']) ? strtolower(trim($_POST['name'])) : null;
    $data->description = isset($_POST['description']) ? trim($_POST['description']) : null;

    include("datasets/create.php");
});

// Create an endpoint to get the info about a dataset.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/info.php");
});

// Create an endpoint to get the info about a dataset.
route::delete("/datasets/(\w+|\-+)\.(\w+|\-+)", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/remove.php");
});

// Create an endpoint to perform a query on a dataset.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/data", function($prefix, $name) use($data) {
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
route::post("/datasets/(\w+|\-+)\.(\w+|\-+)/data", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->row = isset($_POST['row']) ? json_decode($_POST['row'], true) : null;
    $data->rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;

    include("datasets/data/insert.php");
});

// Create an endpoint to update data in the dataset.
route::put("/datasets/(\w+|\-+)\.(\w+|\-+)/data", function($prefix, $name) use($data) {
    parse_str(file_get_contents("php://input"), $_PUT);

    $data->prefix = $prefix;
    $data->name = $name;

    $data->query = isset($_PUT['query']) ? json_decode($_PUT['query'], true) : null;
    $data->changes = isset($_PUT['changes']) ? json_decode($_PUT['changes'], true) : null;

    include("datasets/data/update.php");
});

// Create an endpoint to delete data from the dataset.
route::delete("/datasets/(\w+|\-+)\.(\w+|\-+)/data", function($prefix, $name) use($data) {
    parse_str(file_get_contents("php://input"), $_DELETE);

    $data->prefix = $prefix;
    $data->name = $name;

    $data->query = isset($_DELETE['query']) ? json_decode($_DELETE['query'], true) : null;

    include("datasets/data/delete.php");
});

// Create an endpoint to insert new data into the dataset.
route::put("/datasets/(\w+|\-+)\.(\w+|\-+)/upload/(\w+|\-+)", function($prefix, $name, $type) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->type = in_array(strtolower($type), array("csv", "xlsx", "ods")) ? strtolower($type) : null;

    include("datasets/upload.php");
});

// Create an endpoint to list the constraints on a dataset.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/constraints", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/constraints.php");
});

// Create an endpoint to add a constraint to a dataset.
route::post("/datasets/(\w+|\-+)\.(\w+|\-+)/constraints", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->field = isset($_POST['field']) ? trim($_POST['field']) : null;
    $data->type = isset($_POST['type']) ? trim(strtolower($_POST['type'])) : null;

    include("datasets/constraints/add.php");
});

// Create an endpoint to remove a constraint on a dataset.
route::delete("/datasets/(\w+|\-+)\.(\w+|\-+)/constraints", function($prefix, $name) use($data) {
    parse_str(file_get_contents("php://input"), $_DELETE);

    $data->prefix = $prefix;
    $data->name = $name;

    $data->field = isset($_DELETE['field']) ? trim($_DELETE['field']) : null;

    include("datasets/constraints/remove.php");
});

// Create an endpoint to list the indexes on a dataset.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/indexes", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/indexes.php");
});

// Create an endpoint to add an index to a dataset.
route::post("/datasets/(\w+|\-+)\.(\w+|\-+)/indexes", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->fields = isset($_POST['fields']) ? json_decode($_POST['fields'], true) : null;

    include("datasets/indexes/add.php");
});

// Create an endpoint to list the access to a dataset.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/access", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    include("datasets/access.php");
});

// Create an endpoint to add access to a dataset.
route::post("/datasets/(\w+|\-+)\.(\w+|\-+)/access", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->type = isset($_POST['type']) ? $_POST['type'] : null;
    $data->username = isset($_POST['username']) ? trim(strtolower($_POST['username'])) : null;

    include("datasets/access/add.php");
});

// Create an endpoint to remove access to a dataset.
route::delete("/datasets/(\w+|\-+)\.(\w+|\-+)/access", function($prefix, $name) use($data) {
    parse_str(file_get_contents("php://input"), $_DELETE);

    $data->prefix = $prefix;
    $data->name = $name;

    $data->type = isset($_DELETE['type']) && in_array($_DELETE['type'], array("read", "write")) ? trim(strtolower($_DELETE['type'])) : null;
    $data->username = isset($_DELETE['username']) ? trim(strtolower($_DELETE['username'])) : null;

    include("datasets/access/remove.php");
});

// Create an endpoint for the polyfit calculations.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/calc/polyfit", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->fields = isset($_GET['fields']) ? json_decode($_GET['fields'], true) : null;
    $data->degree = isset($_GET['degree']) && $_GET['degree'] > 0 && $_GET['degree'] <= 20 ? (int)$_GET['degree'] : 2;

    include("datasets/calc/polyfit.php");
});

// Create an endpoint for the stats calculations.
route::get("/datasets/(\w+|\-+)\.(\w+|\-+)/calc/stats", function($prefix, $name) use($data) {
    $data->prefix = $prefix;
    $data->name = $name;

    $data->field = isset($_GET['field']) ? trim($_GET['field']) : null;
    $data->query = isset($_GET['query']) ? json_decode($_GET['query'], true) : null;

    include("datasets/calc/stats.php");
});

/*!
 * Perform the routing request.
 */

route::parse();

include("main.php");
exit;

?>
