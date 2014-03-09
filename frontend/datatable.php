<?php
require_once("../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;

$datasetInfo = $rainhawk->fetchDataset($dataset);

if(stristr($datasetInfo["message"], "Invalid Mashape key"))
{
    echo json_encode("Invalid mashape key");
    exit;
}

$query = isset($_GET['query']) ? json_decode($_GET['query']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$fields = isset($_GET['fields']) ? json_decode($_GET['fields']) : $datasetInfo["fields"];
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;


$data = $rainhawk->selectData($dataset, $query, $offset, $limit, $sort, $fields)["results"];
$result = array($fields);
for($i=0; $i<count($data); $i++)
{
    var_dump($data[$i]);
    echo "<br><br>";
    $values = array();
    foreach($fields as $field => $field_val)
    {
        $values[] = isset($data[$i][$field]) ? $field_val : null;
    }
    $result[] = $values;
}

echo json_encode($result);



?>

