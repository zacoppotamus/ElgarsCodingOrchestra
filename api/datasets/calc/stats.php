<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Check that the parameters have all been set and sent to the script,
 * including the prefix and the name.
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
    "min" => null,
    "max" => null,
    "average" => null,
    "sum" => null,
);

/*!
 * Run some commands to calculate some aggregations for the dataset
 * depending on the query provided.
 */

// Check if we have a field_name or not.
if(empty($data->field)) {
    echo json_beautify(json_render_error(404, "You didn't specify a field name to use in the calculations."));
    exit;
}

// Set some local variables.
$query = array();

// Check if we need to run a pre-query to match certain documents.
if(!empty($data->query)) {
    $query[] = array(
        '$match' => $data->query
    );
}

// Create the query that we need to run for the calculations.
$query[] = array(
    '$group' => array(
        "_id" => null,
        "min" => array(
            '$min' => '$' . $data->field
        ),
        "max" => array(
            '$max' => '$' . $data->field
        ),
        "average" => array(
            '$avg' => '$' . $data->field
        ),
        "sum" => array(
            '$sum' => '$' . $data->field
        )
    )
);

// Run the query.
$result = $dataset->aggregate($query);

// Check if it worked.
if(!$result) {
    echo json_beautify(json_render_error(405, "An unexpected error occured while performing your query - are you sure you formatted it correctly?"));
    exit;
}

// Set the JSON output.
$stats = $result[0];

$json['min'] = $stats['min'];
$json['max'] = $stats['max'];
$json['average'] = $stats['average'];
$json['sum'] = $stats['sum'];

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>