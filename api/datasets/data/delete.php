<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Check that the parameters have all been set and sent to the script,
 * including the prefix and the name.
 */

if(empty($data->prefix) || empty($data->name)) {
    echo json_beautify(json_render_error(401, "You didn't pass one or more of the required parameters."));
    exit;
}

/*!
 * Check to see if the dataset exists, and that we have access to it.
 * We need to use the prefix and the name of the dataset to get a
 * reference to it.
 */

// Create a new dataset object.
$dataset = new rainhawk\dataset($data->prefix, $data->name);

// Check that the dataset exists.
if(!$dataset->exists) {
    echo json_beautify(json_render_error(402, "The dataset you specified does not exist."));
    exit;
}

// Check that we can write to the dataset.
if(!$dataset->have_write_access(app::$mashape_key)) {
    echo json_beautify(json_render_error(403, "You don't have access to write to this dataset."));
    exit;
}

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "deleted" => 0
);

/*!
 * Check that the required parameters are set, so that we can
 * ensure that no data gets erroneously removed.
 */

// Check the query is set.
if(empty($data->query)) {
    echo json_beautify(json_render_error(404, "You can't use a catch-all query for delete statements, dummy."));
    exit;
}

/*!
 * Run the delete command directly in MongoDB, but be careful that
 * they're not accidentally sending an empty query to delete all
 * documents.
 */

// Set some local variables.
$query = $data->query;

print_r($data->query);

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

// Run the delete query.
$query = $dataset->delete($query);

// Check if the query failed.
if(!$query) {
    echo json_beautify(json_render_error(405, "An unexpected error occured while performing your query - are you sure you formatted all the parameters correctly?"));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>