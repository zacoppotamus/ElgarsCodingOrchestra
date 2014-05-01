<?php

require_once "../includes/core.php";
require_once "../includes/check_login.php";

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$result = $rainhawk->deleteDataset($dataset);

if($result) {
    header("Location: /datasets.php?deleted");
    exit;
} else {
    header("Location: /datasets.php?deletefailed");
    exit;
}

?>

