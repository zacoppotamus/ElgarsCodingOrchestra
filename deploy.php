<?php

// Define the headers.
header("content-type: text/plain; charset=utf8");

// Define the commands to be run.
$commands = array(
    "git pull",
    "git status",
    "git submodule sync",
    "git submodule update",
    "git submodule status"
);

// Run them all.
foreach($commands as $command) {
    $output = shell_exec($command);

    echo "$ {$command}\n";
    echo "{$output}\n";
}

?>