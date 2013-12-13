<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(JSON_ERROR_NOMETHOD);
exit;

?>