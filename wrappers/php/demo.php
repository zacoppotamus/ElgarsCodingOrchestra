<?php

// Include the Rainhawk class.
include("rainhawk.class.php");

// Create the new instance.
$rainhawk = new Rainhawk("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

// List the datasets that we have access to.
$datasets = $rainhawk->datasets();

// Create a new dataset, which will receive a prefix.
$result = $rainhawk->createDataset("phpusers", "An example dataset from the PHP class for testing.");

// Check if it worked.
if(!$result) {
    echo "Could not create dataset - " . $rainhawk->error();
    exit;
}

// Get the full name of the dataset.
$name = $result['name'];

// Now we want to get the dataset information.
$info = $rainhawk->fetchDataset($name);

// Check if it worked.
if(!$result) {
    echo "Could not fetch dataset - " . $rainhawk->error();
    exit;
}

// If it did, list the dataset information.
print_r($result);

?>