<?php

include("eco.class.php");
header("content-type: application/json; charset=utf8");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$idvalue = isset($_POST['_id']) ? $_POST['_id'] : null;

$query = array(
    "_id" => $idvalue
);

$result = $eco->delete($dataset, $query);

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
