<?php

include("eco.class.php");
header("content-type: application/json; charset=utf8");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
//$dataset = "nysubway";
$document = array();

foreach($_POST as $name => $value) {
    if($name == "dataset") continue;

    $document[$name] = $value;
}

$result = $eco->insert($dataset, $document);

if(!$result) {
    echo json_encode(array(
        "Result" => "ERROR",
        "Message" => $eco->error()
    ));
    exit;
}

echo json_encode(array(
    "Result" => "OK",
    "Record" => $document
));

?>
