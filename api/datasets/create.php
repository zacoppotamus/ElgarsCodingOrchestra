<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "indexes" => (isset($_POST['indexes'])) ? json_decode($_POST['indexes']) : array()
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "name" => $data['dataset'],
    "rows" => 0,
    "indexes" => array()
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to create."));
    exit;
}

/*!
 * Perform our collection create statement in MongoDB, and if we
 * have any indexes to create then create them.
 */

try {
    $collection = mongocli::create_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "There was a problem creating the new dataset - maybe it already exists or maybe there's no disk space left?"));
    exit;
}

/*!
 * If we have any indexes, create them using the provided field
 * names.
 */

if(isset($data['indexes']) && !empty($data['indexes']) && is_array($data['indexes'])) {
    try {
        foreach($data['indexes'] as $field) {
            $collection->ensureIndex(array($field => 1), array("background" => true));
        }
    } catch(Exception $e) {
        echo json_beautify(json_render_error(403, "An unexpected error occured while adding one of your indexes."));
        exit;
    }
}

/*!
 * Return the new collection information using some stats collected
 * from the MongoDB cursor.
 */

$indexes = $collection->getIndexInfo();

foreach($indexes as $index) {
    $json['indexes'][$index['name']] = $index['key'];
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>