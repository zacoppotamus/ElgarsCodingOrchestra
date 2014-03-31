<?php

// Include the Rainhawk class.
include("rainhawk.class.php");
header("content-type: text/plain; charset=utf8");

// Create a debugging function.
function debug($message) {
    echo "[+] " . $message . "\n";
}

// Create a failure logging function.
function failed($message) {
    global $failed;

    $failed++;

    echo "[!] " . $message . "\n";
}

// Create the new instance.
$rainhawk = new Rainhawk("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");
$started = microtime(true);
$username = null;
$name = "phpwrapper";
$failed = 0;

// Create an array of the tests.
$tests = array(
    /**
     * Test #1: Ping the API.
     */

    function() use($rainhawk) {
        global $username;

        debug("Checking that the API is online...");

        $ping = $rainhawk->ping();
        if($ping == false) {
            failed("Could not ping the service - " . $rainhawk->error());
        } else {
            $username = $ping['mashape_user'];

            debug("--> Server time offset: " . (time() - $ping['server_time']));
            debug("--> Found username: " . $username);
        }
    },

    /**
     * Test #2: List the datasets, delete the dataset if it already exists,
     * create a dataset and then get information on the dataset.
     */

    function() use($rainhawk) {
        global $username, $name;

        debug("Fetching a list of the datasets that we have access to...");

        $datasets = $rainhawk->listDatasets();
        if($datasets == false) {
            failed("Could not list the datasets - " . $rainhawk->error());
        } else {
            debug("--> Found a list of datasets.");

            foreach($datasets as $dataset) {
                if($dataset['name'] == $username . "." . $name) {
                    debug("--> Found " . $dataset['name'] . ", cleaning up...");
                    $rainhawk->deleteDataset($dataset['name']);
                }
            }
        }

        debug("Creating the test dataset, '" . $name . "'...");

        $dataset = $rainhawk->createDataset($name, "An example dataset from the PHP class for testing.");
        if($dataset == false) {
            failed("Could not create the dataset - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($dataset));
        }

        $name = $username . "." . $name;

        debug("Fetching the information for the new dataset...");

        $dataset = $rainhawk->fetchDataset($name);
        if($dataset == false) {
            failed("Could not fetch the dataset - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($dataset));
        }
    },

    /**
     * Test #3: Add one row of data, add another two rows of data and upload
     * a file to the dataset.
     */

    function() use($rainhawk) {
        global $name;

        debug("Adding one row of data to the dataset...");

        $row = array(
            "name" => "John",
            "age" => 20,
            "weight" => 320.2,
            "role" => "content"
        );

        $inserted = $rainhawk->insertData($name, $row);
        if($inserted == false) {
            failed("Could not insert one row of data - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($inserted));
        }

        debug("Adding two rows of data in batch to the dataset...");

        $rows = array(
            array(
                "name" => "Jane",
                "age" => 24,
                "weight" => 220.4,
                "role" => "owner"
            ),
            array(
                "name" => "Bob",
                "age" => 36,
                "weight" => 320.6,
                "role" => "manager"
            )
        );

        $inserted = $rainhawk->insertMultiData($name, $rows);
        if($inserted == false) {
            failed("Could not insert two rows of data in batch - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($inserted));
        }

        debug("Uploading some rows of data to the dataset from a .csv...");

        $file = "../test_data.csv";

        $uploaded = $rainhawk->uploadData($name, $file);
        if($uploaded == false) {
            failed("Could not upload the data into the dataset - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($uploaded));
        }
    },

    /**
     * Test #4: Detect the constraints that should be applied to the data, and
     * then remove one of the constraints and list the constraints.
     */

    function() use($rainhawk) {
        global $name;

        debug("Automatically detecting constraints and applying them...");

        $constraints = $rainhawk->addConstraint($name);
        if($constraints == false) {
            failed("Could not detect the constraints - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($constraints));
        }

        debug("Removing the constraint on 'role'...");

        $removed = $rainhawk->removeConstraint($name, "role");
        if($removed == false) {
            failed("Could not remove the constraint on 'role' - " . $rainhawk->error());
        } else {
            debug("--> Removed: " . json_encode($removed));
        }

        debug("Listing all constraints being applied to the dataset...");

        $constraints = $rainhawk->listConstraints($name);
        if($constraints == false) {
            failed("Could not list the constraints - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($constraints));
        }
    },

    /**
     * Test #5: Add an index to all of the fields on the dataset, remove one
     * of the indexes and then list them.
     */

    function() use($rainhawk) {
        global $name;

        debug("Adding indexes to all of the relevant fields automatically...");

        $indexes = $rainhawk->addIndex($name);
        if($indexes == false) {
            failed("Could not add the indexes - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($indexes));
        }

        debug("Listing all indexes currently on the dataset...");

        $indexes = $rainhawk->listIndexes($name);
        if($indexes == false) {
            failed("Could not list the indexes - " . $rainhawk->error());
        } else {
            debug("--> " . json_encode($indexes));
        }
    }
);

// Run the tests.
foreach($tests as $callable) {
    $callable();
}

// Finish up and exit.
debug("Done! All tests completed in " . number_format(microtime(true) - $started, 2) . " second(s).");

// Check for any failures.
if($failed > 0) {
    failed("--> " . $failed . " operation(s) failed.");
}

// Finish execution.
exit;

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