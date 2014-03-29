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
    <title>Constraints</title>
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
        <h1>Constraints</h1>
        <h3>Assign constraints to a dataset</h3>
      </div>
      <div class='row'>
        <form role='form' class='form-horizontal'>
<?php
for($i=0; $i<count($fields); $i++)
{
  if($fields[$i] != "_id")
  {
    echo <<<EOD
            <div class='form-group'>
              <label class='control-label col-sm-3' for='field$fields[$i]'>$fields[$i]</label>
              <div class='col-sm-9'>
                <select id='$fields[$i]' class='form-control'>
                  <option value='string'>String</option>
                  <option value='integer'>Integer</option>
                  <option value='float'>Float</option>
                  <option value='timestamp'>Timestamp</option>
                  <option value='latitude'>Latitude</option>
                  <option value='longitude'>Longitude</option>
                </select>
              </div>
            </div>

EOD;
  }
}

?>
          <button type='button' class="btn btn-default">Submit</button>
        </form>
      </div>
    </div>
  </body>
</html>

