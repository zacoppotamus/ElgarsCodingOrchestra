<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Define our server_time timestamp and set it as the JSON output
 * to be sent back to the client.
 */

$json = array(
    "server_time" => time(),
    "mashape_user" => app::$username
);

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>