<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['d'])) ? trim(strtolower($_GET['d'])) : null,
    "offset" => (isset($_GET['offset']) && intval($_GET['offset']) >= 0) ? intval($_GET['offset']) : 0,
    "rows" => (isset($_GET['rows']) && intval($_GET['rows']) >= 1) ? intval($_GET['rows']) : -1,
    "fields" => (isset($_GET['fields'])) ? json_decode($_GET['fields'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "rows" => 0,
    "offset" => $data['offset'],
    "results" => array()
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

$collection = mongocli::select_collection($data['dataset']);

/*!
 * Perform our 'find' query based on the information fed to the
 * API. We have to check if some parameters are set/valid here.
 */

$query = array();
$fields = array();

// Check if any field names were sent.
if(isset($data['fields']) && !empty($data['fields'])) {
    if(is_array($data['fields'])) {
        $fields = $data['fields'];
    } else {
        echo json_beautify(json_render_error(402, "You didn't specify the field names correctly, they should be in the form: ['field1', 'field2']."));
        exit;
    }
}

// Run the query.
$query = $collection->find($query, $fields);

// Set the offset if we have one.
if($data['offset'] > 0) {
    $query = $query->skip($data['offset']);
}

// If we have a row limit, apply it.
if($data['rows'] > -1) {
    $query = $query->limit($data['rows']);
}

// Iterate through the results and populate the output.
foreach($query as $row) {
    $json['rows']++;
    $json['results'][] = $row;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>