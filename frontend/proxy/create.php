<?php

include("eco.class.php");
header("content-type: application/json; charset=utf8");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$document = isset($_POST['document']) ? json_decode($_POST['document']) : null;
//$documents = isset($_POST['documents']) ? json_decode($_POST['documents']) : null;

$records = array();
$results = $eco->insert($dataset, $document);

if(!$results) {
    echo json_encode(array(
        "Result" => "ERROR",
        "Message" => $eco->error()
    ));
    exit;
}

foreach($results['results'] as $row) {
    $records[] = $row;
}

echo json_encode(array(
    "Result" => "OK",
    "Records" => $records
));

?>
