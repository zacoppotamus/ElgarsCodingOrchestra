<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Check if the user provided enough information to create the new
 * dataset, including the name and description.
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
if(!$dataset->have_read_access(app::$mashape_key)) {
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
 * Try and add the indexes to the dataset as the user has specified,
 * which will always work even if the index already exists.
 */

// Create a local variable for the fields.
$fields = array();

// Check which fields to index.
if(!empty($data->fields)) {
    $fields = $data->fields;
} else {
    $fields = app::find_index_names($dataset->fields);
}

// Check if we need to add any indexes at all.
if(empty($fields)) {
    echo json_beautify(json_render_error(404, "We couldn't find any indexes to add."));
    exit;
}

// Add each index.
foreach($fields as $field) {
    if(!$dataset->add_index($field)) {
        echo json_beautify(json_render_error(405, "There was a problem while adding an index on '" . $field . "'."));
        exit;
    }
}

// Get a list of indexes.
$indexes = $dataset->fetch_indexes();

// Check if the listing failed.
if(!$indexes) {
    echo json_beautify(json_render_error(406, "There was a problem while fetching the indexes."));
    exit;
}

// Return them into the JSON array.
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