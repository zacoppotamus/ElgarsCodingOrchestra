<?php

include("eco.class.php");
header("content-type: application/json; charset=utf8");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$query = isset($_GET['document']) ? json_decode($_GET['document']) : null;
$query = isset($_GET['documents']) ? json_decode($_GET['documents']) : null;

$records = array();
$results = $eco->insert($dataset, $document, $documents);

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
