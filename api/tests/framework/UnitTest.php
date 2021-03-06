<?php

/**
 * @package UnitTest
 */

class UnitTest {
    /**
     * @var string
     */

    protected $class;

    /**
     * @var integer
     */

    protected $passed = 0;

    /**
     * @var integer
     */

    protected $failed = 0;

    /**
     * @var integer
     */
    protected $started;

    /**
     * @param mixed $value
     * @param mixed $expected
     * @return mixed
     */

    public function assertEquals($value, $expected) {
        return $this->outputTestResult($value == $expected);
    }

    /**
     * @param mixed $value
     * @return mixed
     */

    public function assert($value) {
        return $this->outputTestResult($value);
    }

    /**
     * @param boolean $passes
     * @return mixed
     */

    public function outputTestResult($passes) {
        if($passes) {
            $this->passed++;

            echo ".";
        } else {
            $this->failed++;

            echo "F";
        }
    }

    /**
     * @return UnitTest
     */

    public function __before() {
        return $this;
    }

    /**
     * @return void
     */

    public function exec() {
        $this->started = microtime(true);
        $this->__before();

        echo $this->class . "\n\n";

        foreach(get_class_methods($this) as $method) {
            if(stripos($method, "assert") !== false || in_array($method, array("outputTestResult", "exec", "__before"))) {
                continue;
            } else {
                $this->$method();
                echo "\n";
            }
        }

        echo "\n";
        echo ($this->failed == 0 ? "OK" : "FAILED") . " (" . ($this->passed + $this->failed) . " tests, " . $this->failed . " assertions)\n";
        echo number_format(microtime(true) - $this->started, 2) . " seconds\n";

        return;
    }
}

?>