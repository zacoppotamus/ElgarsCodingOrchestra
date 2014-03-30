<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
  $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;

$datasetInfo = $rainhawk->fetchDataset($dataset);
$fields      = $datasetInfo["fields"];

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user)
{
  header('Location: login.php?dest='.urlencode($_SERVER['REQUEST_URI']));
  exit();
}

?>
<html lan="en-GB">
  <head>
  <title>Properties - <?php echo $dataset; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>

    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
  </head>
  <body>
    <div class='container'>
      <div class='row'>
        <h1>Properties - <?php echo $dataset; ?></h1>
        <h3>
          Dataset properties
          <a href="account.php" type="button" class="btn btn-warning pull-right"><i class="fa fa-bars"></i>&nbsp Datasets</a>
        </h3>
      </div>
      <div class='row'>
        <table class='table'>
          <thead>
            <tr>
              <th>User</th>
              <th>Write Access</th>
              <th>Read Access</th>
            </tr>
            <tr>
          </thead>
          <tbody>
            <tr>
              <td> benelgar </td>
              <td>
                <input type="radio" name="benelgarOptions" id="benelgarWrite" value="write">
              </td>
              <td>
                <input type="radio" name="benelgarOptions" id="benelgarRead" value="read">
              </td>

            </tr>
          </tbody>
        </table>
        <button type='button' class="btn btn-default">Apply</button>
      </div>
    </div>
  </body>
</html>

