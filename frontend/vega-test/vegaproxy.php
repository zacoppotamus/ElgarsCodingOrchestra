<?php
include("../../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$ping = $rainhawk->ping();

if(stristr($ping["message"], "Invalid Mashape key"))
{
    echo json_encode("Invalid mashape key");
    exit;
}

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$query = isset($_GET['query']) ? json_decode($_GET['query']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$fields = isset($_GET['fields']) ? json_decode($_GET['fields']) : null;
$exclude = isset($_GET['exclude']) ? json_decode($_GET['exclude']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;

$records = array();
$results = $rainhawk->selectData($dataset, $query, $offset, $limit, $sort, $fields, $exclude);
var_dump($results);

echo json_encode($results["data"]["results"]);

?>

