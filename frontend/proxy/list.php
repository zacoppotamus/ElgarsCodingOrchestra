<?php

include("eco.class.php");

$eco = new eco("eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$query = isset($_GET['query']) ? json_decode($_GET['query']) : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$fields = isset($_GET['fields']) ? json_decode($_GET['fields']) : null;

$records = array();
$results = $eco->select($dataset, $query, $offset, $limit, $fields);

foreach($results['rows'] as $row) {
    $records[] = $row;
}

echo json_encode(array(
    "Result" => "Ok",
    "Records" => $records
));

?>