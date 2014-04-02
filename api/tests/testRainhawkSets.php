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
    }

    public function testCreate() {
        $dataset = new Dataset(app::$username, "testset");
        $this->assert(sets::create($dataset), true);
    }

    public function testRemove() {
        $dataset = new Dataset(app::$username, "testset");
        $this->assert(sets::remove($dataset), true);
    }
}

$test = new RainhawkSetsTest;
$test->exec();

?>