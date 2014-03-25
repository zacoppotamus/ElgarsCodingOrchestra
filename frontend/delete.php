<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$user         = $rainhawk->ping()["mashape_user"];

if($user === false)
{
    header('Location: login.php?fail');
}

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;

$result = $rainhawk->deleteDataset($dataset);

if($result)
{
    header("Location: account.php?deleted");
}
else
{
    header("Location: account.php?deletefailed");
}

?>

