<?php

include_once "framework/UnitTest.php";
include_once "../includes/kernel.php";

use Rainhawk\Sets;
use Rainhawk\Dataset;

class RainhawkSetsTest extends UnitTest {
    public $class = "Rainhawk\Sets";

    public function __before() {
        app::$username = "test";
        rainhawk::select_database("tests");

        return $this;
    }

    public function testCreate() {
        $dataset = new Dataset(app::$username, "testset");
        $dataset->description = "Test dataset.";
        $dataset->read_access[] = app::$username;
        $dataset->write_access[] = app::$username;

        $this->assert(sets::create($dataset), true);
    }

    public function testUpdate() {
        $dataset = new Dataset(app::$username, "testset");
        $this->assert(sets::update($dataset), true);
    }

    public function testExists() {
        $this->assert(sets::exists(app::$username, "testset"), true);
    }

    public function testFetchMetadata() {
        $metadata = sets::fetch_metadata(app::$username, "testset");
        $description = $metadata['description'];

        $this->assert($description, "Test dataset.");
    }

    public function testSetsForUser() {
        $sets = sets::sets_for_user(app::$username);
        $datasets = array();

        foreach($sets as $set) {
            $datasets[] = $set;
        }

        $this->assert($datasets[0]['description'], "Test dataset.");
    }

    public function testRemove() {
        $dataset = new Dataset(app::$username, "testset");
        $this->assert(sets::remove($dataset), true);
    }
}

$test = new RainhawkSetsTest;
$test->exec();

?>