<?php

class RainhawkTest extends UnitTest {
    public $class = "Rainhawk";

    public function __before() {
        rainhawk::select_database("tests");

        return $this;
    }

    public function testSelectCollection() {
        $this->assert(rainhawk::select_collection("datasets"));
    }
}

?>