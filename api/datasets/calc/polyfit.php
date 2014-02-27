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
if(!$dataset->have_read_access(app::$mashape_key)) {
    echo json_beautify(json_render_error(403, "You don't have access to read from this dataset."));
    exit;
}

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "coefficients" => array()
);

/*!
 * Pipe the command to our Python script to calculate the coefficients
 * of the polynomial using NumPy.
 */

$dataset = escapeshellarg($data->dataset);
$degree = $data->degree;

// Check that the two fields are set.
if(!isset($data->fields) || !is_array($data->fields) || !(count($data->fields) == 2)) {
    echo json_beautify(json_render_error(404, "You didn't specify the two fields to use!"));
    exit;
}

// Set the fields.
$field_one = escapeshellarg($data->fields[0]);
$field_two = escapeshellarg($data->fields[1]);

// Run the command.
exec("python '../correlation/correlation.py' eco " . $dataset . " " . $field_one . " " . $field_two . " " . $degree, $output);

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