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

/*!
 * Come up with a new prefix to use for the dataset, by generating
 * a 6 character string and checking that it hasn't already been
 * used.
 */

do {
    $prefix = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    $exists = rainhawk\sets::exists($prefix, $data->name);

    if($exists) {
        unset($prefix);
    }
} while(!isset($prefix));

/*!
 * Now that we have our prefix and name, we can create the new
 * dataset and return the details to the user.
 */

$dataset = new rainhawk\dataset($data->prefix, $data->name);

$dataset->prefix = $prefix;
$dataset->name = $data->name;
$dataset->description = $data->description;

$dataset->read_access[] = app::$mashape_key;
$dataset->write_access[] = app::$mashape_key;

if(!rainhawk\sets::create($dataset)) {
    echo json_beautify(json_render_error(402, "There was a problem while trying to create your dataset - please try again later."));
    exit;
}

if(!$dataset->add_index(array("_id" => 1))) {
    echo json_beautify(json_render_error(403, "There was a problem while trying to create your dataset - please try again later."));
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
    "fields" => $dataset->fields,
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