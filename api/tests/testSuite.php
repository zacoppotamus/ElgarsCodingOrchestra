<?php

$files = glob("test*.php");

foreach($files as $index => $file) {
    if($file == "testSuite.php") unset($files[$index]);
}

foreach($files as $index => $file) {
    include_once $file;

    if($index < count($files) - 1) {
        echo "\n";
    }
}

?>