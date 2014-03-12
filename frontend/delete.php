<?php
require_once("../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$user         = $rainhawk->ping()["mashape_user"];

if ($user == false)
{
    header('Location: login.php?fail');
}

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;

$result = $rainhawk->deleteDataset($dataset);
var_dump($result);

?>

