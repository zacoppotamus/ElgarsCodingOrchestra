<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "fields" => (isset($_GET['fields'])) ? json_decode($_GET['fields'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "indexes" => array()
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to add indexes to."));
    exit;
}

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Run the ensureIndex command on the specified dataset so that we
 * can make sure that we have indexes that the user wants.
 */

$fields = array();

// Check which fields to index.
if(isset($data['fields'])) {
    $fields = $data['fields'];
} else {
    try {
        $query = $collection->find(array(), array("_id" => false));
        $query->limit(10);

        foreach($query as $row) {
            $fields = array_unique(array_keys($row) + $fields);
        }

        $fields = app::find_index_names($fields);
    } catch(Exception $e) {
        echo json_beautify(json_render_error(403, "An unknown error occured while finding the field names to autoindex."));
        exit;
    }
}

// Check if we need to add any indexes at all.
if(!empty($fields)) {
    // Run the insertion query.
    try {
        $status = $collection->ensureIndex($fields, array("background" => true));

        if($status['ok'] == 1) {
            $json['indexes'] = $collection->getIndexInfo();
        } else {
            echo json_beautify(json_render_error(405, "An unknown error occured while adding the indexes to the dataset."));
            exit;
        }
    } catch(Exception $e) {
        echo json_beautify(json_render_error(404, "An unknown error occured while adding the indexes to the dataset."));
        exit;
    }
} else {
    $json['indexes'] = $collection->getIndexInfo();
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>