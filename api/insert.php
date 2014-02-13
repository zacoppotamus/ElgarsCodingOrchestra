<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_POST['dataset'])) ? trim(strtolower($_POST['dataset'])) : null,
    "document" => (isset($_POST['document'])) ? json_decode($_POST['document'], true) : null,
    "documents" => (isset($_POST['documents'])) ? json_decode($_POST['documents'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "documents" => array()
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to insert your documents into."));
    exit;
}

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Add the specified document(s) to the Mongo collection, ignoring any
 * fields and just straight up dumping the values.
 */

$documents = array();

// Check for some actual documents.
if(!empty($data['document'])) {
    $documents[] = $data['document'];
} else if(!empty($data['documents'])) {
    foreach($data['documents'] as $document) {
        $documents[] = $document;
    }
} else {
    echo json_beautify(json_render_error(403, "You didn't specify any documents to insert."));
    exit;
}

// Run the insertion query.
try {
    $status = $collection->batchInsert($documents);

    if($status['ok'] == 1) {
        $json['documents'] = $documents;
    } else {
        echo json_beautify(json_render_error(405, "An unknown error occured while inserting your data into the database."));
        exit;
    }
} catch(Exception $e) {
    echo json_beautify(json_render_error(404, "An unknown error occured while inserting your data into the database."));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>