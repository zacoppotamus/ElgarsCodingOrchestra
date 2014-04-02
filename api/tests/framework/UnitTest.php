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

    public function assert($value, $expected) {
        return $this->outputTestResult($value == $expected);
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
     * @return void
     */

    public function exec() {
        $this->started = microtime(true);

        echo $this->class . "\n\n";

        foreach(get_class_methods($this) as $method) {
            if(stripos($method, "assert") !== false || $method == "outputTestResult" || $method == "exec") {
                continue;
            } else {
                $this->$method();
                echo "\n";
            }
        }

        echo "\n";
        echo ($this->failed == 0 ? "OK" : "FAILED") . " (" . ($this->passed + $this->failed) . " tests, " . $this->failed . " assertions)\n";

        return;
    }
}

?>