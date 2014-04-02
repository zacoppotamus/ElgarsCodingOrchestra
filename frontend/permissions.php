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

$access     = $rainhawk->listAccess($dataset);
$readList   = $access["read_access"];
$writeList  = $access["write_access"];
$accessList = array_unique(array_merge($readList, $writeList));

if(!in_array($user, $writeList))
{
  header("Location: properties.php?dataset=$dataset&nowrite");
  exit();
}

$currentUsers = $_POST["currentUser"];
$newUsers     = $_POST["newUser"];

$errors = array();

foreach ($currentUsers as $username => $access)
{
  $result = "";
  $types = array();

  if(isset($access['read'])) $types[] = "read";
  if(isset($access['write'])) $types[] = "write";

  foreach($types as $type)
  {
    if($type == "read" && !in_array($username, $readList))
    {
      $result = $rainhawk->giveAccess($dataset, $username, "read");
    }
    elseif($type == "write" && !in_array($username, $writeList))
    {
      $result = $rainhawk->giveAccess($dataset, $username, "write");
    }
    else
    {
      $result = true;
    }

    if(!$result)
    {
      $errors[] = $rainhawk->error();
    }
  }
}

foreach ($readList as $username) {
  if(!isset($currentUsers[$username]['read']))
  {
    $result = $rainhawk->removeAccess($dataset, $username, "read");

    if(!$result)
    {
      $errors[] = $rainhawk->error();
    }
  }
}

foreach ($writeList as $username) {
  if(!isset($currentUsers[$username]['write']))
  {
    $result = $rainhawk->removeAccess($dataset, $username, "write");

    if(!$result)
    {
      $errors[] = $rainhawk->error();
    }
  }
}

foreach ($newUsers as $key=>$newUser)
{
  $result = "";
  $types = array();

  if(isset($newUser['read'])) $types[] = "read";
  if(isset($newUser['write'])) $types[] = "write";

  if(in_array($newUser["user"], array_keys($currentUsers)))
  {
    $errors[] = "User $newUser[user] already has permissions assigned";
  }
  else
  {
    $result = $rainhawk->giveAccess($dataset, $newUser["user"], $types);
  }

  if(!$result)
  {
    $errors[] = $rainhawk->error();
  }
}

if(empty($errors))
{
  header("Location: properties.php?dataset=$dataset");
  exit();
}

var_dump($errors);
exit();

?>
