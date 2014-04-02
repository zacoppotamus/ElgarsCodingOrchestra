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
if(!$dataset->have_write_access(app::$username)) {
    echo json_beautify(json_render_error(403, "You don't have access to write to this dataset."));
    exit;
}

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array();

/*!
 * Try and remove the index from the dataset provided the field has already
 * been specified.
 */

// Check if the field is set.
if(empty($data->field)) {
    echo json_beautify(json_render_error(404, "You didn't specify the field to remove the index on."));
    exit;
}

// Check if the _id field has been speciifed.
if($data->field == "_id") {
    echo json_beautify(json_render_error(405, "You can't remove the index on the _id field."));
    exit;
}

// Get the current indexes.
$indx = $dataset->fetch_indexes();
$indexes = array();

// Set the indexes properly.
foreach($indx as $index) {
    $indexes[] = array_keys($index['key'])[0];
}

// Check if the index has already been set.
if(!in_array($data->field, $indexes)) {
    echo json_beautify(json_render_error(406, "The field you specified does not have an index."));
    exit;
}

// Remove the index.
if(!$dataset->remove_index($data->field)) {
    echo json_beautify(json_render_error(407, "There was an unknown problem removing the index you specified."));
    exit;
}

// Set the output.
$json['removed'] = true;

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>