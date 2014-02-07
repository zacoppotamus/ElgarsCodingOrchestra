<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "rows" => 0,
    "min" => null,
    "max" => null,
    "mean" => null,
    "stddev" => null
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to query."));
    exit;
}

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Run some commands to calculate some aggregations for the dataset
 * depending on the query provided.
 */

$query = array(
    '$group' => array(
        '$min' => "age"
    )
);

// Run the query.
try {
    $results = $collection->aggregate($query);

    var_dump($results);
    exit;
} catch(Exception $e) {
    echo json_beautify(json_render_error(403, "An unexpected error occured while performing your query - are you sure you formatted it correctly?"));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>