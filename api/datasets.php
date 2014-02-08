<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array();

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array(
    "datasets" => array()
);

// Run the query to find the datasets.
try {
    $json['datasets'] = mongocli::get_collections();
} catch(Exception $e) {
    echo json_beautify(json_render_error(401, "An unexpected error occured while trying to find the datasets."));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>