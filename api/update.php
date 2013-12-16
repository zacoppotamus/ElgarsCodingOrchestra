<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_POST['dataset'])) ? trim(strtolower($_POST['dataset'])) : null,
    "query" => (isset($_POST['query'])) ? json_decode($_POST['query'], true) : null,
    "changes" => (isset($_POST['changes'])) ? json_decode($_POST['changes'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "updated" => 0
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to update your documents in."));
    exit;
}

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Run the update command directly in MongoDB - we don't really need
 * to worry about doing this manually.
 */

$query = $data['query'];
$changes = $data['changes'];

// Check the query is set.
if(!isset($data['query']) || empty($data['query'])) {
    echo json_beautify(json_render_error(403, "You can't use a catch-all query for update statements, dummy."));
    exit;
}

// Check the changes aren't empty.
if(!isset($data['changes']) || empty($data['changes'])) {
    echo json_beautify(json_render_error(404, "You didn't specify any changes to make."));
    exit;
}

// Run the update query.
try {
    $status = $collection->update($query, $changes, array("multiple" => true));

    if($status['ok'] == 1) {
        $json['updated'] = (int)$status['n'];
    } else {
        echo json_beautify(json_render_error(406, "An unexpected error occured while trying to update the documents."));
        exit;
    }
} catch(Exception $e) {
    echo json_beautify(json_render_error(405, "An unexpected error occured while trying to update the documents."));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>