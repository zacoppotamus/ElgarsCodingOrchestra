<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "query" => (isset($_GET['query'])) ? json_decode($_GET['query'], true) : null,
    "offset" => (isset($_GET['offset']) && intval($_GET['offset']) >= 0) ? intval($_GET['offset']) : 0,
    "limit" => (isset($_GET['limit']) && intval($_GET['limit']) >= 1) ? intval($_GET['limit']) : -1,
    "sort" => (isset($_GET['sort'])) ? json_decode($_GET['sort'], true) : null,
    "fields" => (isset($_GET['fields'])) ? json_decode($_GET['fields'], true) : null,
    "exclude" => (isset($_GET['exclude'])) ? json_decode($_GET['exclude'], true) : null
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "rows" => 0,
    "offset" => $data['offset'],
    "results" => array()
);

/*!
 * Select the relevant dataset inside the database. If the collection
 * doesn't already exist, then Mongo will automatically create it
 * when new data is inserted.
 */

if(!isset($data['dataset']) || empty($data['dataset'])) {
    echo json_beautify(json_render_error(401, "You didn't specify a dataset to query."));
    exit;
}

$dataset = new rainhawk\dataset($data['dataset']);

if(!$dataset->exists) {
    echo json_beautify(json_render_error(402, "The dataset that you specified does not exist."));
    exit;
}

if(!$dataset->have_read_access(app::$mashape_key)) {
    echo json_beautify(json_render_error(403, "You don't have read access for this dataset."));
    exit;
}

/*!
 * Perform our 'find' query based on the information fed to the
 * API. We have to check if some parameters are set/valid here.
 */

$query = array();
$fields = array();

// Check if we have a query or not.
if(isset($data['query']) && !empty($data['query'])) {
    $query = $data['query'];
}

// Check if any field names were sent.
if(isset($data['fields']) && !empty($data['fields'])) {
    if(is_array($data['fields'])) {
        foreach($data['fields'] as $field_name) {
            $fields[$field_name] = true;
        }

        if(!isset($fields['_id'])) {
            $fields['_id'] = false;
        }
    } else {
        echo json_beautify(json_render_error(403, "You didn't specify the field names to return correctly, they should be in the form: ['field1', 'field2']."));
        exit;
    }
}

// Check if we need to exclude fields.
if(isset($data['exclude']) && !empty($data['exclude'])) {
    if(is_array($data['exclude'])) {
        foreach($data['exclude'] as $field_name) {
            $fields[$field_name] = false;
        }
    } else {
        echo json_beautify(json_render_error(404, "You didn't specify the field names to exclude correctly, they should be in the form: ['field1', 'field2']."));
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
try {
    $query = $collection->find($query, $fields);

    // Get the number of rows the query matches.
    $json['rows'] = $query->count();

    // Sort the query using the provided query.
    if(isset($data['sort'])) {
        $query = $query->sort($data['sort']);
    }

    // Set the offset if we have one.
    if($data['offset'] > 0) {
        $query = $query->skip($data['offset']);
    }

    // If we have a row limit, apply it.
    if($data['limit'] > -1) {
        $query = $query->limit($data['limit']);
        $json['limit'] = $data['limit'];
    }

    // Iterate through the results and populate the output.
    foreach($query as $row) {
        if(isset($row['_id'])) {
            $row['_id'] = (string)$row['_id'];
        }

        $json['results'][] = $row;
    }
} catch(Exception $e) {
    echo json_beautify(json_render_error(405, "An unexpected error occured while performing your query - are you sure you formatted all the parameters correctly?"));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>