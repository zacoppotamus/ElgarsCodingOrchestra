<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
  $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user)
{
  header('Location: login.php?dest='.urlencode($_SERVER['REQUEST_URI']));
  exit();
}

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;

$datasetInfo = $rainhawk->fetchDataset($dataset);
$fields      = $datasetInfo["fields"];
$constraints = $datasetInfo["constraints"];

$errors = array();

if(isset($_GET[ "autoapply" ]))
{
  $result = $rainhawk->addConstraint($dataset);
}
else
{
  foreach($_POST["constraint"] as $field => $constraint)
  {
    if($constraints[$field] != $constraint)
    {
      if(isset($constraints[$field]))
      {
        $result = $rainhawk->removeConstraint($dataset, $field);
        if(isset($result["message"]))
        {
          $errors[] = $result["message"];
        }
      }

      if($constraint != "none")
      {
        $result = $rainhawk->addConstraint($dataset, $field, $constraint);
        if(isset($result["message"]))
        {
          $errors[] = $result["message"];
        }
      }
    }
  }
}

header("Location: properties.php?dataset=$dataset");

?>
