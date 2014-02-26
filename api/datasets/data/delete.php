<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "query" => (isset($_POST['query'])) ? json_decode($_POST['query'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "deleted" => 0
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to delete documents from."));
    exit;
}

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Run the delete command directly in MongoDB, but be careful that
 * they're not accidentally sending an empty query to delete all
 * documents.
 */

$query = $data['query'];

// Check the query is set.
if(!isset($data['query']) || empty($data['query'])) {
    echo json_beautify(json_render_error(403, "Woah, you can't delete everything! You probably don't need to do that."));
    exit;
}

// Change the MongoID if we have one.
foreach($query as $key => $value) {
    if($key == "_id") {
        try {
            $mongoid = new MongoID($value);
        } catch(Exception $e) {
            $mongoid = null;
        }

        $query[$key] = $mongoid;
    }
}

// Run the update query.
try {
    $status = $collection->remove($query, array("justOne" => false));

    if($status['ok'] == 1) {
        $json['deleted'] = (int)$status['n'];
    } else {
        echo json_beautify(json_render_error(406, "An unexpected error occured while trying to delete the documents."));
        exit;
    }
} catch(Exception $e) {
    echo json_beautify(json_render_error(405, "An unexpected error occured while trying to delete the documents."));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>