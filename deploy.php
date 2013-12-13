<?php

// Define the headers.
header("content-type: text/plain; charset=utf8");

// Define the commands to be run.
$commands = array(
    "/usr/bin/git pull 2&>1",
    "/usr/bin/git status 2&>1",
    "/usr/bin/git submodule sync 2&>1",
    "/usr/bin/git submodule update 2&>1",
    "/usr/bin/git submodule status 2&>1"
);

// Run them all.
foreach($commands as $command) {
    $output = shell_exec($command);

    echo "$ {$command}\n";
    echo "{$output}\n";
}

?>