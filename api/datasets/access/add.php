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
    echo json_beautify(json_render_error(404, "You didn't specify the type of access to give."));
    exit;
}

// Check if the username is set.
if(empty($data->username)) {
    echo json_beautify(json_render_error(404, "You didn't specify the user to give access to."));
    exit;
}

// If the type is JSON, decode it into an array.
$data->type = json_decode($data->type, true) ?: $data->type;

// Iterate through the type or types set and apply them.
if(is_string($data->type)) {
    // Make sure the type is valid.
    if(!in_array($data->type, array("read", "write"))) {
        echo json_beautify(json_render_error(405, "You didn't specify a valid type of access to give."));
        exit;
    }

    // Check if the user already has that access.
    if(in_array($data->username, $dataset->{$data->type . "_access"})) {
        echo json_beautify(json_render_error(406, "The user you specified already has " . $data->type . " access to this dataset."));
        exit;
    }

    // Give the user access.
    $dataset->{$data->type . "_access"}[] = $data->username;
    $dataset->{$data->type . "_access"} = array_unique($dataset->{$data->type . "_access"});
} else {
    // Iterate through the types.
    foreach($data->type as $type) {
        // Make sure the type is valid.
        if(!in_array($type, array("read", "write"))) {
            echo json_beautify(json_render_error(405, "You didn't specify a valid type of access to give."));
            exit;
        }

        // Check if the user already has that access.
        if(in_array($data->username, $dataset->{$type . "_access"})) {
            echo json_beautify(json_render_error(406, "The user you specified already has " . $type . " access to this dataset."));
            exit;
        }

        // Give the user access.
        $dataset->{$type . "_access"}[] = $data->username;
        $dataset->{$type . "_access"} = array_unique($dataset->{$type . "_access"});
    }
}

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