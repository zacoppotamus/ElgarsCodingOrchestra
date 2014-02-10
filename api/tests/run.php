<?php

include("includes/kernel.php");
include("includes/api/core.php");
include("includes/classes/eco.class.php");

/*!
 * Create a new instance of ECO with our Mashape key.
 */

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

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
        echo "[!] {$test_name}: Failed! - (#" . $eco->errno() . ") " . $eco->error() . "\n";
    }
}

// Define our dataset name and the failed tests.
$dataset = "apitests";
$passed = 0;
$failed = 0;

/*!
 * Test zero: Ping  request
 */

$ping = $eco->ping();
check_test("Ping", $ping);

/*!
 * Test one: Inserting data.
 */

$document = array(
    "first_name" => "John",
    "last_name" => "Doe",
    "lives_in" => "BS1",
    "age" => 18
);

$insert = $eco->insert($dataset, $document);
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

$insert = $eco->insert_multi($dataset, $documents);
check_test("Multi Insert", $insert);

/*!
 * Test two: Select data.
 */

$search = $eco->select($dataset, array(
    "first_name" => "James"
));

check_test("Select #1 - first_name = James, rows: " . $search['rows'], $search);

$search = $eco->select($dataset, array(
    "last_name" => "Cash"
));

check_test("Select #2 - last_name = Cash, rows: " . $search['rows'], $search);

$search = $eco->select($dataset);

check_test("Select #3 - All records, rows: " . $search['rows'], $search);

/*!
 * Test three: Update data.
 */

$update = $eco->update($dataset, array(
    "first_name" => "James"
), array(
    "\$set" => array(
        "first_name" => "Jacob"
    )
));

check_test("Update #1 - James => Jacob, updated: " . $update['updated'], $update);

$search = $eco->select($dataset, array(
    "first_name" => "James"
));

check_test("Select #4 - first_name = James, rows: " . $search['rows'], $search);

/*!
 * Test four: Delete data.
 */

$delete = $eco->delete($dataset, array(
    "first_name" => "Jacob"
));

check_test("Delete #1 - first_name = Jacob, deleted: " . $delete['deleted'], $delete);

$delete = $eco->delete($dataset, array(
    "age" => 18
));

check_test("Delete #2 - age = 18, deleted: " . $delete['deleted'], $delete);

$delete = $eco->delete($dataset, array(
    "\$or" => array(
        array("first_name" => "Matthew"),
        array("first_name" => "John")
    )
));

check_test("Delete #3 - first_name = Matthew \$or first_name = John, deleted: " . $delete['deleted'], $delete);

$search = $eco->select($dataset);

check_test("Select #4 - All records, rows: " . $search['rows'], $search);

/*!
 * Finish up.
 */

echo "[+] All tests complete! {$passed} passed, {$failed} failed.\n";

?>