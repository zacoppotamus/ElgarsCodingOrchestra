<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "field_name" => (isset($_GET['field_name'])) ? trim(strtolower($_GET['field_name'])) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "rows" => 0,
    "min" => null,
    "max" => null,
    "average" => null
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

// Check if we have a query or not.
if(!isset($data['field_name']) || empty($data['field_name'])) {
    echo json_beautify(json_render_error(403, "You didn't specify a field name to use in the calculations."));
    exit;
}

// Create the query that we need to run for the calculations.
$query = array(
    '$group' => array(
        "_id" => 0,
        "min" => array(
            '$min' => '$' . $data['field_name']
        ),
        "max" => array(
            '$max' => '$' . $data['field_name']
        ),
        "average" => array(
            '$avg' => '$' . $data['field_name']
        )
    )
);

// Run the query.
try {
    $result = $collection->aggregate($query);
    $stats = $result['result'][0];

    $json['min'] = $stats['min'];
    $json['max'] = $stats['max'];
    $json['average'] = $stats['average'];
} catch(Exception $e) {
    echo json_beautify(json_render_error(404, "An unexpected error occured while performing your query - are you sure you formatted it correctly?"));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>