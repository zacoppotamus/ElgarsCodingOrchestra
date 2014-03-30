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
 * Try and add the access to the dataset as the user has specified,
 * which will always work even if the access already exists.
 */

// Check if the type is set.
if(empty($data->type)) {
    echo json_beautify(json_render_error(404, "You didn't specify the type of access to remove."));
    exit;
}

// Check if the username is set.
if(empty($data->username)) {
    echo json_beautify(json_render_error(404, "You didn't specify the user to remove access from."));
    exit;
}

// Check if the user already has that access or not.
if(!in_array($data->username, $dataset->{$data->type . "_access"})) {
    echo json_beautify(json_render_error(405, "The user you specified does not have " . $data->type . " access to this dataset."));
    exit;
}

// Remove the user's access.
$dataset->{$data->type . "_access"} = array_diff($dataset->{$data->type . "_access"}, array($data->username));

// Store the dataset information in the index table.
\rainhawk\sets::update($dataset);

// Return the removed attribute to the JSON.
$json['removed'] = true;

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>