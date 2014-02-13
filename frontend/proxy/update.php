<?php

include("eco.class.php");
header("content-type: application/json; charset=utf8");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

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

$result = $eco->update($dataset, $query, $changes);

if(!$result) {
    echo json_encode(array(
        "Result" => "ERROR",
        "Message" => $eco->error()
    ));
    exit;
}

echo json_encode(array(
    "Result" => "OK"
));

?>
