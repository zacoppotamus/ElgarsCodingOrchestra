<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "datasets" => array()
);

// Perform a query to find the datasets.
$datasets = rainhawk\sets::sets_for_user(app::$username);

// Check if that query worked.
if(!$datasets) {
    echo json_beautify(json_render_error(401, "There was a problem while trying to find your datasets."));
    exit;
}

// Iterate through the results, if any.
foreach($datasets as $dataset) {
    $json['datasets'][] = array(
        "name" => $dataset['prefix'] . "." . $dataset['name'],
        "description" => $dataset['description'],
        "rows" => $dataset['rows'],
        "fields" => array_keys($dataset['fields']),
        "read_access" => $dataset['read_access'],
        "write_access" => $dataset['write_access']
    );
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>