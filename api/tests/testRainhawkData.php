<?php

include_once "framework/UnitTest.php";
include_once "../includes/kernel.php";

use Rainhawk\Data;

class RainhawkDataTest extends UnitTest {
    public $class = "Rainhawk\Data";

    public function testCheckString() {
        $this->assert(data::check(data::STRING, "test"), "test");
        $this->assert(data::check(data::STRING, "2"), "2");
        $this->assert(data::check(data::STRING, array()), null);
    }

    public function testCheckInteger() {
        $this->assert(data::check(data::INTEGER, 2), 2);
        $this->assert(data::check(data::INTEGER, 2.2), null);
        $this->assert(data::check(data::INTEGER, "2"), 2);
        $this->assert(data::check(data::INTEGER, "2.5ab"), null);
        $this->assert(data::check(data::INTEGER, array()), null);
    }

    public function testCheckFloat() {
        $this->assert(data::check(data::FLOAT, 2), 2);
        $this->assert(data::check(data::FLOAT, 2.2), 2.2);
        $this->assert(data::check(data::FLOAT, "2"), 2);
        $this->assert(data::check(data::FLOAT, "2.2"), 2.2);
        $this->assert(data::check(data::FLOAT, ".5"), 0.5);
        $this->assert(data::check(data::FLOAT, "5.1.2"), null);
        $this->assert(data::check(data::FLOAT, array()), null);
    }

    public function testCheckTimestamp() {
        $this->assert(data::check(data::TIMESTAMP, 2), null);
        $this->assert(data::check(data::TIMESTAMP, "19 July 2002"), strtotime("19 July 2002"));
        $this->assert(data::check(data::TIMESTAMP, "1335939007"), strtotime("1335939007"));
    }

    public function testCheckLatitude() {
        $this->assert(data::check(data::LATITUDE, 2), null);
        $this->assert(data::check(data::LATITUDE, "123.123"), null);
        $this->assert(data::check(data::LATITUDE, "38.898556"), 38.898556);
        $this->assert(data::check(data::LATITUDE, "0.2123123"), null);
    }

    public function testCheckLongitude() {
        $this->assert(data::check(data::LONGITUDE, 2), null);
        $this->assert(data::check(data::LONGITUDE, "-77.037852"), -77.037852);
        $this->assert(data::check(data::LONGITUDE, "38.898556"), 38.898556);
        $this->assert(data::check(data::LONGITUDE, "0.2123123"), null);
    }

    public function testCheckArray() {
        $this->assert(data::check(data::ARR, 13), null);
        $this->assert(data::check(data::ARR, (object)array(1)), array(1));
        $this->assert(data::check(data::ARR, array(1, 2)), array(1, 2));
    }

    public function testDetect() {
        $this->assert(data::detect("test"), data::STRING);
        $this->assert(data::detect("1"), data::INTEGER);
        $this->assert(data::detect("1.2"), data::FLOAT);
        $this->assert(data::detect("-1.4"), data::FLOAT);
        $this->assert(data::detect(array(1)), data::ARR);
    }
}

$test = new RainhawkDataTest;
$test->exec();

?>