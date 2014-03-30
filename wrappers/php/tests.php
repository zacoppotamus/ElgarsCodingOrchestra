<?php

// Include the Rainhawk class.
include("rainhawk.class.php");
header("content-type: text/plain; charset=utf8");

// Create the new instance.
$rainhawk = new Rainhawk("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");
$started = microtime(true);
$username = null;
$name = "phpwrapper";
$full_name = null;

/**
 * Run a ping command to ensure that the API is online, and so that we can
 * fetch our Mashape username for later tests.
 *
 * @covers Rainhawk::ping()
 */

echo "[+] Checking that the API is online...\n";
$ping = $rainhawk->ping();

if(!$ping) {
    echo "[!] Could not ping the service - " . $rainhawk->error() . "\n";
    exit;
}

$username = $ping['mashape_user'];
$full_name = $username . "." . $name;
echo "[+] --> Found username: '$username'.\n";

/**
 * Fetch all of the datasets that the current API key has access to,
 * so that we can check for the existence of the test dataset and remove
 * it if the tests failed previously.
 *
 * @covers Rainhawk::datasets()
 * @covers Rainhawk::deleteDataset()
 */

echo "[+] Fetching list of datasets that we have access to...\n";
$datasets = $rainhawk->datasets();

foreach($datasets as $dataset) {
    if($dataset['name'] == $full_name) {
        echo "[+] --> Found '$full_name', removing...\n";

        if(!$rainhawk->deleteDataset($dataset['name'])) {
            echo "[!] Could not delete dataset - " . $rainhawk->error() . "\n";
            exit;
        }
    }
}

/**
 * Create a new dataset, using a predefined name which will not already exist
 * thanks to the above test. We save the created dataset's name for use
 * elsewhere in the script.
 *
 * @covers Rainhawk::createDataset()
 */

echo "[+] Creating new dataset '$name'...\n";
$create = $rainhawk->createDataset($name, "An example dataset from the PHP class for testing.");

// Check if it worked.
if(!$create) {
    echo "[!] Could not create dataset - " . $rainhawk->error() . "\n";
    exit;
}

// Get the full name of the dataset.
$name = $create['name'];
unset($full_name);

/**
 * Fetch detailed information about a single dataset, provided we have access
 * to read from it.
 *
 * @covers Rainhawk::fetchDataset()
 */

echo "[+] Fetching the new dataset, '$name'...\n";
$dataset = $rainhawk->fetchDataset($name);

if(!$dataset) {
    echo "[!] Could not fetch dataset - " . $rainhawk->error() . "\n";
    exit;
}

print_r($dataset);

/**
 * Insert one row into our test dataset, making sure that the record is
 * inserted properly and has a unique identifier.
 *
 * @covers Rainhawk::insertData()
 * @covers Rainhawk::insertMultiData()
 */

$data = array(
    "name" => "John",
    "age" => 20,
    "weight" => 320,
    "role" => array("admin", "manager", "content")
);

echo "[+] Inserting one row into '$name'...\n";
$data = $rainhawk->insertData($name, $data);

if(!$data) {
    echo "[!] Could not insert data - " . $rainhawk->error() . "\n";
    exit;
}

print_r($data);

/**
 * Test uploading data to our dataset, using the provided test_set.ods file.
 *
 * @covers Rainhawk::uploadData()
 */

$data = "../test_data.csv";

echo "[+] Uploading '$data' to the dataset...\n";
$data = $rainhawk->uploadData($name, $data);

if(!$data) {
    echo "[!] Could not upload data - " . $rainhawk->error() . "\n";
    exit;
}

print_r($data);

/**
 * Set the constraints on our data automatically.
 *
 * @covers Rainhawk::addConstraint()
 */

echo "[+] Automatically detecting constraints and applying them...\n";
$constraints = $rainhawk->addConstraint($name);

if(!$constraints) {
    echo "[!] Could not add constraints - " . $rainhawk->error() . "\n";
    exit;
}

print_r($constraints);

/**
 * List the constraints currently being applied to the data.
 *
 * @covers Rainhawk::listConstraints()
 */

echo "[+] Listing the constraints being applied to the data...\n";
$constraints = $rainhawk->listConstraints($name);

if(!is_array($constraints)) {
    echo "[!] Could not list constraints - " . $rainhawk->error() . "\n";
    exit;
}

print_r($constraints);

/**
 * Remove one of the constraints.
 *
 * @covers Rainhawk::listConstraints()
 */

echo "[+] Removing the constraint on 'price'...\n";
$removed = $rainhawk->removeConstraint($name, "price");

if(!$removed) {
    echo "[!] Could not remove constraint - " . $rainhawk->error() . "\n";
    exit;
}

/**
 * Test selecting data from the dataset, so that we can make sure that complex
 * queries are being run.
 *
 * @covers Rainhawk::selectData()
 */

$query = array(
    "roles" => array(
        '$in' => array("content")
    )
);

echo "[+] Selecting all rows that are content creators...\n";
$rows = $rainhawk->selectData($name, $query);

if(!$rows) {
    echo "[!] Could not select data - " . $rainhawk->error() . "\n";
    exit;
}

print_r($rows);

/**
 * Delete the rows that we have inserted so that we can clean out the indexes
 * and test this command before removing the dataset.
 *
 * @covers Rainhawk::deleteData()
 */

$query = array(
    "name" => "John"
);

echo "[+] Deleting our test data...\n";
$deleted = $rainhawk->deleteData($name, $query);

if(!$deleted) {
    echo "[!] Could not delete data - " . $rainhawk->error() . "\n";
    exit;
}

print_r($deleted);

/**
 * Remove our test dataset, to ensure that the script doesn't leave any broken
 * data on the server.
 *
 * @covers Rainhawk::deleteDataset()
 */

echo "[+] Removing the test dataset '$name'...\n";

if(!$rainhawk->deleteDataset($name)) {
    echo "[!] Could not delete dataset - " . $rainhawk->error() . "\n";
    exit;
}

echo "[+] Done! All tests passed in " . number_format(microtime(true) - $started, 2) . " second(s).\n";

?>