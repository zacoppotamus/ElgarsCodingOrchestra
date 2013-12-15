<?php

include("includes/kernel.php");
include("includes/api/core.php");
include("includes/classes/eco.class.php");

/*!
 * Define a simple test command woohoo!
 */

function check_test($test_name, $result) {
    global $passed, $failed;

    if($result) {
        $passed++;
        echo "[+] {$test_name}: Success.\n";
    } else {
        $failed++;
        echo "[!] {$test_name}: Failed! - (#" . eco::errno() . ") " . eco::error() . "\n";
    }
}

// Define our dataset name and the failed tests.
$dataset = "apitests";
$passed = 0;
$failed = 0;

/*!
 * Test one: Inserting data.
 */

$document = array(
    "first_name" => "John",
    "last_name" => "Doe",
    "lives_in" => "BS1",
    "age" => 18
);

$insert = eco::insert($dataset, $document);
check_test("Single Insert", $insert);

$documents = array();

$documents[] = array(
    "first_name" => "James",
    "last_name" => "Dean",
    "lives_in" => "BS2",
    "age" => 24
);

$documents[] = array(
    "first_name" => "Matthew",
    "last_name" => "Cash",
    "lives_in" => "BS1",
    "age" => 19
);

$insert = eco::insert_multi($dataset, $documents);
check_test("Multi Insert", $insert);

/*!
 * Test two: Select data.
 */

$search = eco::select($dataset, array(
    "first_name" => "James"
));

check_test("Select #1", $search);

/*!
 * Test three: Update data.
 */

// Update some data.

/*!
 * Test four: Delete data.
 */

// Delete all the data.

/*!
 * Finish up.
 */

echo "[+] All tests complete! {$passed} passed, {$failed} failed.\n";

?>