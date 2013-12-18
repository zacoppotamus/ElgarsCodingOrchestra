<?php

include("includes/kernel.php");
include("includes/api/core.php");
include("includes/classes/eco.class.php");

/*!
 * Define a function to turn a CSV into an array.
 */

function csv_to_array($filename = "", $delimiter = ",") {
    if(!file_exists($filename) || !is_readable($filename)) {
        return false;
    }

    $header = null;
    $data = array();

    if(($handle = fopen($filename, "r")) !== false) {
        while(($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if(!$header) {
                $header = $row;
                var_dump($row);
            } else {
                $data[] = array_combine($header, $row);
                var_dump($row);
            }
        }

        fclose($handle);
    }

    return $data;
}

// Define our dataset name.
$dataset = "nysubway";

// Get our data.
$data = csv_to_array("../frontend/visualizations/nysubway/subentrances.csv");

// Import data.
if(!empty($data)) {
    $insert = eco::insert_multi($dataset, $data);

    if($insert) {
        echo "[+] Added!";
    } else {
        echo "[!] Failed! " . eco::error();
    }
}

?>