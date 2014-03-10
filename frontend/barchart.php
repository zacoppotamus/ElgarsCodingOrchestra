<?php
require_once("../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$ping = $rainhawk->ping();

if(stristr($ping["message"], "Invalid Mashape key"))
{
    echo json_encode("Invalid mashape key");
    exit;
}

$dataset = isset($_GET['dataset']) ? $_GET['dataset'] : null;
$fields = $rainhawk->fetchDataset($dataset)["fields"];

?>
<html>
  <head>
    <title>Google Chart Visualisation</title>

    <!-- Bootstrap -->
    <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.css">

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="js/jquery-1.10.2.js"></script>
    <script type="text/javascript">

    // Load the Visualization API and the piechart package.
    google.load('visualization', '1', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

    google.visualization.events.addListener(chart, "error", function(err){
        alert(err);
    });

    function drawChart() {
      var jsonData = $.ajax({
          url: "http://project.spe.sneeza.me/datatable.php?dataset=benelgar.test&fields=[%22" +
            document.getElementById("xName").value +"%22,%22" +
            document.getElementById("y1Name").value +"%22,%22" +
            document.getElementById("y2Name").value + "%22]",
          dataType:"json",
          async: false
          }).responseText;

      // Create our data table out of JSON data loaded from server.
      var data = new google.visualization.arrayToDataTable(JSON.parse(jsonData));

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
      chart.draw(data, {title: "Crimes by Type"});
    }

    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
    <div class="container">
      <div class="row">
        <h1>Google Chart Visualisation</h1>
        <h3>Select data and parameters for your visualisation</h3>
      </div>
      <div class="row">
        <div role="form" class="form-inline">
          <div class="form-group">
            <label for="xName">Ordinal Data</label>
            <select name="xName" id="xName" onchange="drawChart()" class="form-control">
              <?php
              for($i=0; $i<count($fields); $i++)
              {
                  if($fields[$i] != "_id")
                  {
                    echo "<option value=$fields[$i]>$fields[$i]</option>";
                  }
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="y1Name">Continuous Data</label>
            <select name="y1Name" id="y1Name" onchange="drawChart()" class="form-control">
              <?php
              for($i=0; $i<count($fields); $i++)
              {
                  if($fields[$i] != "_id")
                  {
                    echo "<option value=$fields[$i]>$fields[$i]</option>";
                  }
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="y2Name">Continuous Data</label>
            <select name="y2Name" id="y2Name" onchange="drawChart()" class="form-control">
              <?php
              for($i=0; $i<count($fields); $i++)
              {
                  if($fields[$i] != "_id")
                  {
                    echo "<option value=$fields[$i]>$fields[$i]</option>";
                  }
              }
              ?>
            </select>
          </div>
      </div>
      <div class="row">
        <div id="chart_div" style="height: 80%"></div>
      </div>
  </body>
</html>

