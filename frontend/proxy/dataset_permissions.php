<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$access = $rainhawk->listAccess($dataset);
$readList = $access['read_access'];
$writeList = $access['write_access'];
$accessList = array_unique(array_merge($readList, $writeList));

if(!in_array($user, $writeList)) {
    header("Location: /properties.php?dataset=" . $dataset . "&nowrite");
    exit;
}

$currentUsers = (array)$_POST['currentUser'];
$newUsers = (array)$_POST['newUser'];
$errors = array();

foreach($currentUsers as $username => $access) {
    $result = "";
    $types = array();

    if(isset($access['read'])) $types[] = "read";
    if(isset($access['write'])) $types[] = "write";

    foreach($types as $type) {
        if($type == "read" && !in_array($username, $readList)) {
            $result = $rainhawk->giveAccess($dataset, $username, "read");
        } else if($type == "write" && !in_array($username, $writeList)) {
            $result = $rainhawk->giveAccess($dataset, $username, "write");
        } else {
            $result = true;
        }

        if(!$result) {
            $errors[] = $rainhawk->error();
        }
    }
}

foreach($readList as $username) {
    if(!isset($currentUsers[$username]['read'])) {
        $result = $rainhawk->removeAccess($dataset, $username, "read");

        if(!$result) {
            $errors[] = $rainhawk->error();
        }
    }
}

foreach($writeList as $username) {
    if(!isset($currentUsers[$username]['write'])) {
        $result = $rainhawk->removeAccess($dataset, $username, "write");

        if(!$result) {
            $errors[] = $rainhawk->error();
        }
    }
}

foreach($newUsers as $key => $newUser) {
    $result = "";
    $types = array();

    if(isset($newUser['read'])) $types[] = "read";
    if(isset($newUser['write'])) $types[] = "write";

    if(in_array($newUser["user"], array_keys($currentUsers))) {
        $errors[] = "User " . $newUser['user'] . " already has permissions assigned";
    } else {
        $result = $rainhawk->giveAccess($dataset, $newUser["user"], $types);
    }

    if(!$result) {
        $errors[] = $rainhawk->error();
    }
}

if(empty($errors)) {
    header("Location: /properties.php?dataset=" . $dataset);
    exit;
}

var_dump($errors);
exit;

?>