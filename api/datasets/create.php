<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Check if the user provided enough information to create the new
 * dataset, including the name and description.
 */

if(empty($data->name) || empty($data->description)) {
    echo json_beautify(json_render_error(401, "You didn't pass one or more of the required parameters."));
    exit;
}

if(!preg_match("/^[a-zA-Z0-9\-\_]+$/", $data->name)) {
    echo json_beautify(json_render_error(402, "The dataset name you passed contains disallowed characters!"));
    exit;
}

/*!
 * Come up with a new prefix to use for the dataset, by generating
 * a 6 character string and checking that it hasn't already been
 * used. Now we can just use their username.
 */

$data->prefix = app::$username;
$dataset = new rainhawk\dataset($data->prefix, $data->name);

// Check if you already have a dataset with this name.
if($dataset->exists) {
    echo json_beautify(json_render_error(403, "You already have a dataset with this name!"));
    exit;
}

/*!
 * Now that we have our prefix and name, we can create the new
 * dataset and return the details to the user.
 */

$dataset->prefix = $data->prefix;
$dataset->name = $data->name;
$dataset->description = $data->description;

// Give this user read and write access.
$dataset->read_access[] = app::$username;
$dataset->write_access[] = app::$username;

// Perform the creation command.
if(!rainhawk\sets::create($dataset)) {
    echo json_beautify(json_render_error(404, "There was a problem while trying to create your dataset - please try again later."));
    exit;
}

// Get a new reference to the dataset.
$dataset = new rainhawk\dataset($data->prefix, $data->name);

// Create an index on _id to force-create the set.
if(!$dataset->add_index("_id")) {
    echo json_beautify(json_render_error(405, "There was a problem while trying to create your dataset - please try again later."));
    exit;
}

/*!
 * Define our output by filling up the JSON array with the variables
 * from the dataset object.
 */

$json = array(
    "name" => $dataset->prefix . "." . $dataset->name,
    "description" => $dataset->description,
    "rows" => $dataset->rows,
    "fields" => array_keys($dataset->fields),
    "constraints" => $dataset->constraints,
    "read_access" => $dataset->read_access,
    "write_access" => $dataset->write_access
);

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>