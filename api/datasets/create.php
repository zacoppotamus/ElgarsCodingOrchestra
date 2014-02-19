<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "name" => (isset($_POST['name'])) ? trim($_POST['name']) : null,
    "description" => (isset($_POST['description'])) ? trim($_POST['description']) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "label" => null,
    "name" => $data['name'],
    "description" => $data['description']
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['name']) || empty($data['name'])) {
    echo json_beautify(json_render_error(401, "You didn't specify the name of the dataset to create."));
    exit;
}

if(!isset($data['description']) || empty($data['description'])) {
    echo json_beautify(json_render_error(402, "You didn't specify a description for the new dataset."));
    exit;
}

/*!
 * Perform our collection create statement in MongoDB, and if we
 * have any indexes to create then create them.
 */

try {
    $collection = mongocli::create_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "There was a problem creating the new dataset - maybe there's no disk space left?"));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>