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

$json = array(
    "added" => false
);

/*!
 * Try and add the access to the dataset as the user has specified,
 * which will always work even if the access already exists.
 */

// Check if the field is set.
if(!isset($data->field)) {
    echo json_beautify(json_render_error(404, "You didn't specify the field to add a constraint to."));
    exit;
}

// Check if the field already has a constraint.
if(isset($dataset->constraints[$data->field])) {
    echo json_beautify(json_render_error(405, "This field already has a constraint, please remove the current constraint before adding a new one."));
    exit;
}

// Check if the datatype has been set.
if(!isset($data->type) || !in_array($data->type, array("string", "integer", "float", "timestamp", "latitude", "longitude"))) {
    echo json_beautify(json_render_error(406, "The data type that you've selected is not supported. We currently support: string, integer, float, timestamp, latitude, longitude."));
    exit;
}

// Add the constraint to the field.
$dataset->constraints[$data->field] = array(
    "type" => $data->type
);

// Store the dataset information in the index table.
\rainhawk\sets::update($dataset);

// Return the added attribute to the JSON.
$json['added'] = true;

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>