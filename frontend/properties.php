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

$access     = $rainhawk->fetchAccessList($dataset);
$readList   = $access["read_access"];
$writeList  = $access["write_access"];
$accessList = array_unique(array_merge($readList, $writeList));

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
        <form method="post">
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
            <?php
              for($i=0; $i<count($accessList); $i++)
              {
                echo <<<EOD
                <tr>
                  <td>$accessList[$i]</td>
                  <td>
                    <input type="radio" name="$accessList[$i]" value="write">
                  </td>
                  <td>
                    <input type="radio" name="$accessList[$i]" value="read">
                  </td>
                </tr>
EOD;
              }
            ?>
            </tbody>
          </table>
          </form>
        <button type='button' class="btn btn-default">Apply</button>
      </div>
    </div>
  </body>
</html>

