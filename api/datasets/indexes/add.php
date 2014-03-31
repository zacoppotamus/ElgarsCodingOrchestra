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
 * Try and add the indexes to the dataset as the user has specified,
 * which will always work even if the index already exists.
 */

// Check if the fields are set.
if(!empty($data->field)) {
    // Check if the field can be indexed.
    if($data->field == "_id") {
        echo json_beautify(json_render_error(404, "The _id field is always indexed by default."));
        exit;
    }

    // Get the current indexes.
    $indexes = $dataset->fetch_indexes();

    // Iterate through to find the indexes.
    foreach($indexes as $field => $key) {
        if($data->field == $field) {
            echo json_beautify(json_render_error(405, "The field you specified already has an index."));
            exit;
        }
    }

    // Add the index.
    if(!$dataset->add_index($data->field)) {
        echo json_beautify(json_render_error(406, "There was an unknown problem adding the index you specified."));
        exit;
    }

    // Set the JSON response.
    $json['added'] = true;
} else {
    // Find the fields to index and the indexes.
    $fields = app::find_index_names(array_keys($dataset->fields));
    $indx = $dataset->fetch_indexes();
    $indexes = array();

    // Set the indexes properly.
    foreach($indx as $index) {
        $indexes[] = array_keys($index['key'])[0];
    }

    // Remove the fields that already have indexes.
    foreach($fields as $index => $field) {
        if(in_array($field, $indexes)) unset($fields[$index]);
    }

    // Check if the fields are empty.
    if(empty($fields)) {
        echo json_beautify(json_render_error(404, "We couldn't find any fields to add indexes to."));
        exit;
    }

    // Start buffering the response.
    $json['detected'] = array();

    // Add indexes to each of the fields.
    foreach($fields as $field) {
        if(!$dataset->add_index($field)) {
            echo json_beautify(json_render_error(405, "There was an unknown problem adding one of the indexes."));
            exit;
        }

        // Set the response.
        $json['detected'][] = $field;
    }
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>