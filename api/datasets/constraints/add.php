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
 * Try and add the constraint to the field that was specified, but if none
 * were specified then try and automatically detect which ones to add.
 */

// Check if the field is set.
if(!empty($data->field)) {
    // Check if the field already has a constraint.
    if(isset($dataset->constraints[$data->field])) {
        echo json_beautify(json_render_error(405, "This field already has a constraint, please remove the current constraint before adding a new one."));
        exit;
    }

    // Check if the datatype has been set.
    if(empty($data->type) || !in_array($data->type, array("string", "integer", "float", "timestamp", "latitude", "longitude"))) {
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
} else {
    // Get all the records to find all the fields.
    $rows = $dataset->find(array());
    $fields = array();

    // Iterate through the rows.
    foreach($rows as $row) {
        foreach($row as $field => $value) {
            if(isset($fields[$field])) {
                $fields[$field] = \rainhawk\data::detect($value);
            } else {
                $type = \rainhawk\data::detect($value);

                // Check if the field type is in-line.
                if($fields[$field] == $type) {
                    continue;
                } else if($fields[$field] == \rainhawk\data::INTEGER && $type == \rainhawk\data::FLOAT) {
                    $fields[$field] == \rainhawk\data::FLOAT;
                } else if($fields[$field] == \rainhawk\data::FLOAT && $type == \rainhawk\data::INTEGER) {
                    continue;
                } else {
                    $fields[$field] = "unknown";
                }
            }
        }
    }

    // Strip the already-constrained fields.
    foreach($dataset->constraints as $field => $constraint) {
        unset($fields[$field]);
    }

    // Check if there are any constraints to add.
    if(empty($fields)) {
        echo json_beautify(json_render_error(407, "No unconstrained fields were found during automatic detection."));
        exit;
    }

    // Set the JSON array.
    $json['detected'] = array();

    // Add the new constraints.
    foreach($fields as $field => $type) {
        if($type == "unknown") {
            // Set the JSON output.
            $json['detected'][] = array(
                "field" => $field,
                "type" => $type
            );
        } else {
            // Set the constraint on the field.
            $dataset->constraints[$field] = array(
                "type" => $type
            );

            // Store the dataset information in the index table.
            \rainhawk\sets::update($dataset);

            // Set the JSON output.
            $json['detected'][] = array(
                "field" => $field,
                "type" => $type
            );
        }
    }
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>