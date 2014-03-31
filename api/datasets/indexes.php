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

// Check that we can read from the dataset.
if(!$dataset->have_read_access(app::$username)) {
    echo json_beautify(json_render_error(403, "You don't have access to read from this dataset."));
    exit;
}

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "indexes" => array()
);

/*!
 * Fetch the indexes from the dataset, and list them back to the
 * user in a friendly format.
 */

// Get a list of indexes.
$indexes = $dataset->fetch_indexes();

// Check if the listing failed.
if(!$indexes) {
    echo json_beautify(json_render_error(404, "There was a problem while fetching the indexes."));
    exit;
}

// Return them into the JSON array.
foreach($indexes as $index) {
    $json['indexes'][] = $index['name'];
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>