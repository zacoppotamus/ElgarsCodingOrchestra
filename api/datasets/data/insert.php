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

// Check that we can write to the dataset.
if(!$dataset->have_write_access(app::$username)) {
    echo json_beautify(json_render_error(403, "You don't have access to write to this dataset."));
    exit;
}

/*!
 * Define our output by filling up the JSON array with the variables
 * from the dataset object.
 */

$json = array(
    "rows" => array()
);

/*!
 * Add the specified row(s) to the Mongo collection, ignoring any
 * fields and just straight up dumping the values.
 */

$rows = array();

// Check for some actual documents.
if(!empty($data->row)) {
    $rows[] = $data->row;
} else if(!empty($data->rows)) {
    foreach($data->rows as $row) $rows[] = $row;
} else {
    echo json_beautify(json_render_error(404, "There were no rows passed to the endpoint to insert."));
    exit;
}

// Insert the rows of data into the dataset.
$rows = $dataset->insert_multi($rows);

// Check that it was a success.
if(!$rows) {
    echo json_beautify(json_render_error(404, "An unknown error occured while inserting your data into the dataset."));
    exit;
}

// Set the JSON output.
foreach($rows as $row) {
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

    $json['rows'][] = $row;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>