<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['dataset'])) ? trim(strtolower($_GET['dataset'])) : null,
    "field_one" => (isset($_GET['field_one'])) ? trim($_GET['field_one']) : null,
    "field_two" => (isset($_GET['field_two'])) ? trim($_GET['field_two']) : null,
    "degree" => (isset($_GET['degree'])) ? (int)$_GET['degree'] : 2,
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "coefficients" => array()
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

try {
    $collection = mongocli::select_collection($data['dataset']);
} catch(Exception $e) {
    echo json_beautify(json_render_error(402, "An unknown error occured while attempting to select the dataset."));
    exit;
}

/*!
 * Pipe the command to our Python script to calculate the coefficients
 * of the polynomial using NumPy.
 */

$dataset = $data['dataset'];
$field_one = null;
$field_two = null;
$degree = 2;

// Check the field_one value.
if(isset($data['field_one']) && !empty($data['field_one']) && is_string($data['field_one'])) {
    $field_one = $data['field_one'];
} else {
    echo json_beautify(json_render_error(403, "You didn't specify the first field to use!"));
    exit;
}

// Check the field_two value.
if(isset($data['field_two']) && !empty($data['field_two']) && is_string($data['field_two'])) {
    $field_one = $data['field_two'];
} else {
    echo json_beautify(json_render_error(404, "You didn't specify the second field to use!"));
    exit;
}

// Check for a degree.
if($data['degree'] > 0 && $data['degree'] < 20) {
    $degree = $data['degree'];
}

// Run the command.
exec("python correlation/correlation.py eco " . $data['dataset'] . " " . $field_one . " " . $field_two . " " . $degree, $output);

// Check for empty output.
if(empty($output)) {
    echo json_beautify(json_render_error(405, "Couldn't find a polyfit equation for the given datasets."));
    exit;
}

// Output the coefficients.
foreach($output as $line) {
    $data = json_decode($line, true);

    if(is_array($data)) {
        $json['coefficients'] = $data;
    }
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>