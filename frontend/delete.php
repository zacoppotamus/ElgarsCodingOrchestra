<?php
include("../../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;

if($rainhawk->deleteDataset($dataset))
{
    header("location: account.php?deleted");
}
else
{
    header("location: account.php?deletefailed");
}

?>

