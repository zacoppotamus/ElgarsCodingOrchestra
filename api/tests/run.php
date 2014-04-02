<?php

require "framework/UnitTest.php";
require "../includes/kernel.php";

array_shift($argv);

if(empty($argv)) {
    echo "Usage: php run.php Class\n";
    exit;
}

if($argv[0] == "suite" || $argv[0] == "all") {
    $files = glob("test*.php");
    $files = array_diff($files, array("testSuite.php"));

    foreach($files as $file) {
        $classes = get_declared_classes();
        require_once $file;
        $diff = array_diff(get_declared_classes(), $classes);
        $class = reset($diff);

        $test = new $class;
        $test->exec();
    }
} else {
    foreach($argv as $class) {
        $class = str_replace("\\", "", $class);
        $file = "test" . $class . ".php";
        $class = $class . "Test";

        if(file_exists($file)) {
            require_once $file;

            $test = new $class;
            $test->exec();
        }
    }
}

?>