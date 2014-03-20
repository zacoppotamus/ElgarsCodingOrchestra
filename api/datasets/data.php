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
if(!$dataset->have_read_access(app::$username)) {
    echo json_beautify(json_render_error(403, "You don't have access to read from this dataset."));
    exit;
}

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "rows" => 0,
    "offset" => $data->offset,
    "results" => array()
);

/*!
 * Perform our 'find' query based on the information fed to the
 * API. We have to check if some parameters are set/valid here.
 */

$query = array();
$fields = array();

// Check if we have a query or not.
if(!empty($data->query)) {
    $query = $data->query;
}

// Check if any field names were sent.
if(!empty($data->fields)) {
    if(is_array($data->fields)) {
        foreach($data->fields as $field_name) {
            $fields[$field_name] = true;
        }

        if(!isset($fields['_id'])) {
            $fields['_id'] = false;
        }
    } else {
        echo json_beautify(json_render_error(404, "You didn't specify the field names to return correctly, they should be in the form: ['field1', 'field2']."));
        exit;
    }
}

// Check if we need to exclude fields.
if(!empty($data->exclude)) {
    if(is_array($data->exclude)) {
        foreach($data->exclude as $field_name) {
            $fields[$field_name] = false;
        }
    } else {
        echo json_beautify(json_render_error(405, "You didn't specify the field names to exclude correctly, they should be in the form: ['field1', 'field2']."));
        exit;
    }
}

// Change the MongoID if we have one.
foreach($query as $key => $value) {
    if($key == "_id") {
        try {
            $mongoid = new MongoID($value);
        } catch(Exception $e) {
            $mongoid = null;
        }

        $query[$key] = $mongoid;
    }
}

// Run the query.
$query = $dataset->find($query, $fields);

// Check if the query failed.
if(!$query) {
    echo json_beautify(json_render_error(406, "An unexpected error occured while performing your query - are you sure you formatted all the parameters correctly?"));
    exit;
}

// Get the number of rows the query matches.
$json['rows'] = $query->count();

// Sort the query using the provided query.
if(isset($data->sort)) {
    try {
        $query = $query->sort($data->sort);
    } catch(Exception $e) {}
}

// Set the offset if we have one.
if($data->offset > 0) {
    $query = $query->skip($data->offset);
}

// If we have a row limit, apply it.
if(isset($data->limit)) {
    $query = $query->limit($data->limit);
    $json['limit'] = $data->limit;
}

// Iterate through the results and populate the output.
foreach($query as $row) {
    if(isset($row['_id'])) {
        $_id = (string)$row['_id'];
        unset($row['id']);
        $row = array("_id" => $_id) + $row;
    }

    foreach($row as $field => $value) {
        if(isset($dataset->constraints[$field])) {
            $row[$field] = \rainhawk\data::check($dataset->constraints[$field]['type'], $value);
        }
    }

    $json['results'][] = $row;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>