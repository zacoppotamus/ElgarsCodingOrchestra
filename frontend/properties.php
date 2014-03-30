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

$readList   = $datasetInfo["read_access"];
$writeList  = $datasetInfo["write_access"];
$accessList = array_unique(array_merge($readList, $writeList));

?>
<!DOCTYPE html>
<html lang="en-GB">
  <head>
  <title>Properties - <?php echo $dataset; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">

    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
  </head>
  <body>
    <div class='container'>
      <div class='row'>
        <h1>Properties - <?php echo $dataset; ?></h1>
        <h3>
          Dataset properties
          <a href="account.php" class="btn btn-warning pull-right"><i class="fa fa-bars"></i>&nbsp; Datasets</a>
        </h3>
      </div>

      <div class="row alert alert-warning">
        <strong>Warning!</strong> This page is still under construction and is not fully functional.
      </div>

      <div class="row">

        <div class="col-md-6">
          <div class='panel panel-default'>
            <div class="panel-heading">
              <h4>Fields</h4>
            </div>
            <div class="panel-body">
              <table class='table'>
                <thead>
                  <tr>
                    <th class="col-md-7">Name</th>
                    <th class="col-md-5">Constraint</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    for($i=0; $i<count($fields); $i++)
                    {
                      if($fields[$i] != "_id")
                      {
                        $type = (count($constraints)>0 && in_array($fields[$i], array_keys($constraints))) ? $constraints[$fields[$i]]["type"] : "none";
                        if(in_array($user, $writeList))
                        {
                          $selected["none"] = "";
                          $selected["integer"] = "";
                          $selected["string"] = "";
                          $selected["latitude"] = "";
                          $selected["longitude"] = "";
                          $selected["float"] = "";
                          $selected["timestamp"] = "";
                          $selected[$type] = "selected";
                          echo <<<EOD
                            <tr>
                              <td>$fields[$i]</td>
                              <td>
                                <select id='$fields[$i]' class='form-control'>
                                  <option value="none" $selected[none]>None</option>
                                  <option value='string' $selected[string]>String</option>
                                  <option value='integer' $selected[integer]>Integer</option>
                                  <option value='float' $selected[float]>Float</option>
                                  <option value='timestamp' $selected[timestamp]>Timestamp</option>
                                  <option value='latitude' $selected[latitude]>Latitude</option>
                                  <option value='longitude' $selected[longitude]>Longitude</option>
                                </select>
                              </td>
                            </tr>
EOD;
                        }
                        else
                        {
                          echo <<<EOD
                            <tr>
                              <td>$fields[$i]</td>
                              <td>$type</td>
                            </tr>
EOD;
                        }
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class='panel panel-default'>
            <div class="panel-heading">
              <h4>Permissions</h4>
            </div>
            <div class="panel-body">
              <form id="accessForm" method="post">
                <table class='table'>
                  <thead>
                    <tr>
                      <th class="col-md-9">User</th>
                      <th class="col-md-1 text-center">Read</th>
                      <th class="col-md-1 text-center">Write</th>
                      <th class="col-md-1 text-center"></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    for($i=0; $i<count($accessList); $i++)
                    {
                      $isWrite      = in_array($accessList[$i], $writeList);
                      $writeChecked = $isWrite ? "checked" : "";
                      $readChecked  = $isWrite ? "" : "checked";
                      echo <<<EOD
                      <tr>
                        <td>$accessList[$i]</td>
                        <td class="text-center">
                          <input type="radio" name="$accessList[$i]" value="read" $readChecked>
                        </td>
                        <td class="text-center">
                          <input type="radio" name="$accessList[$i]" value="write" $writeChecked>
                        </td>
                        <td><a class='btn btn-sm btn-danger'>Revoke</a></td>
                      </tr>
EOD;
                    }
                  ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="text-center" colspan="100%">
                        <a class="btn btn-sm btn-success" href="#">Add User</a>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </form>
            </div>
            <div class="panel-footer text-right">
              <button type='submit' form="accessForm" class="btn btn-default">Apply</button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </body>
</html>

