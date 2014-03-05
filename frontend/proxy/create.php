<?php

include("../../wrappers/php/rainhawk.class.php");
header("content-type: application/json; charset=utf8");

$mashape_key = $_COOKIE["apiKey"];
$rainhawk = new Rainhawk($mashape_key);

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$document = array();

foreach($_POST as $name => $value) {
    if($name == "dataset") continue;

    $document[$name] = $value;
}

$result = $rainhawk->insertData($dataset, $document);

if(!$result) {
    echo json_encode(array(
        "Result" => "ERROR",
        "Message" => $rainhawk->error()
    ));
    exit;
}

echo json_encode(array(
    "Result" => "OK",
    "Record" => $result['documents'][0]
));

?>
