<?php

// Define the headers.
header("content-type: text/plain; charset=utf8");
chdir("/var/www");

// Define the commands to be run.
$commands = array(
    "whoami",
    "/usr/bin/git clean -d -f",
    "/usr/bin/git reset --hard",
    "/usr/bin/git pull",
    "/usr/bin/git status",
    "/usr/bin/git submodule sync",
    "/usr/bin/git submodule update",
    "/usr/bin/git submodule status",
    "cd /var/www/parser && /usr/bin/make clean",
    "cd /var/www/parser && /usr/bin/make"
);

// Run them all.
foreach($commands as $command) {
    echo "$ {$command}\n";

    $output = shell_exec($command . " 2>&1");
    echo "{$output}\n";
}

?>
