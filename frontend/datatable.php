<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$datasetInfo = $rainhawk->fetchDataset($dataset);

$query = isset($_GET['query']) ? json_decode($_GET['query']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$fields = isset($_GET['fields']) ? json_decode($_GET['fields']) : $datasetInfo["fields"];
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;

$data = $rainhawk->selectData($dataset, $query, $offset, $limit, $sort, $fields)["results"];
$result = array($fields);

for($i = 0; $i < count($data); $i++)
{
    $values = array();

    foreach($fields as $field => $field_val)
    {
        $values[] = isset($data[$i][$field_val]) ? $data[$i][$field_val] : null;
    }

    $result[] = $values;
}

echo json_encode($result);

?>

