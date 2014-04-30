<?php

require_once "../includes/core.php";
require_once "../includes/check_login.php";

header("content-type: application/json; charset=utf8");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$idvalue = isset($_POST['_id']) ? $_POST['_id'] : null;
$changes = array();

foreach($_POST as $name => $value) {
    if($name == "dataset") continue;
    if($name == "_id") continue;

    $changes[$name] = $value;
}

$query = array(
    "_id" => $idvalue
);

$changes = array(
    '$set' => $changes
);

$result = $rainhawk->updateData($dataset, $query, $changes);

if(!$result) {
    echo json_encode(array(
        "Result" => "ERROR",
        "Message" => $rainhawk->error()
    ));
    exit;
}

echo json_encode(array(
    "Result" => "OK"
));
exit;

?>
