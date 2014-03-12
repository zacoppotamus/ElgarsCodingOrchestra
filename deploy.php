<?php

// Define the headers.
header("content-type: text/plain; charset=utf8");

// Define the commands to be run.
$commands = array(
    "whoami",
    "/usr/bin/git reset --hard"
    "/usr/bin/git pull",
    "/usr/bin/git status",
    "/usr/bin/git submodule sync",
    "/usr/bin/git submodule update",
    "/usr/bin/git submodule status",
    "cd parser && /usr/bin/make sadparser"
);

// Run them all.
foreach($commands as $command) {
    $output = shell_exec($command);

    echo "$ {$command}\n";
    echo "{$output}\n";
}

?>