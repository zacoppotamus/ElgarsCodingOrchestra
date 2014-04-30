<?php

require_once "../includes/core.php";
require_once "../includes/check_login.php";

header("content-type: application/json; charset=utf8");

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$query = isset($_GET['query']) ? json_decode($_GET['query']) : null;
$limit = isset($_GET['jtPageSize']) ? (int)$_GET['jtPageSize'] : null;
$offset = isset($_GET['jtStartIndex']) ? (int)$_GET['jtStartIndex'] : 0;
$fields = isset($_GET['fields']) ? json_decode($_GET['fields']) : null;
$exclude = isset($_GET['exclude']) ? json_decode($_GET['exclude']) : null;
$sort = isset($_GET['jtSorting']) ? $_GET['jtSorting'] : null;

if(isset($sort)) {
    list($field, $order) = explode(" ", $sort);
    $sort = array($field => ($order == "ASC" ? 1 : -1));
}

$records = array();
$results = $rainhawk->selectData($dataset, $query, $offset, $limit, $sort, $fields, $exclude);

if(!$results) {
    echo json_encode(array(
        "Result" => "Fail",
        "Message" => $rainhawk->error()
    ));
    exit;
}

foreach($results['results'] as $row) {
    $records[] = $row;
}

echo json_encode(array(
    "Result" => "OK",
    "Records" => $records,
    "TotalRecordCount" => $results["rows"]
));
exit;

?>
