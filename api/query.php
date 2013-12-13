<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Grab all of the different inputs so that we can use them elsewhere
 * in this script.
 */

$data = array(
    "dataset" => (isset($_GET['d'])) : trim(strtolower($_GET['d'])) : null,
    "offset" => (isset($_GET['offset']) && intval($_GET['offset']) >= 0) ? intval($_GET['offset']) : 0,
    "rows" => (isset($_GET['rows']) && intval($_GET['rows']) >= 1) ? intval($_GET['rows']) : -1
);

/*!
 * Define an empty array to store the results of whatever we need
 * to send back.
 */

$json = array();

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>