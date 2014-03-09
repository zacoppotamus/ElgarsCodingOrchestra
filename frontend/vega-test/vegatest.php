<?php
require_once("../../wrappers/php/rainhawk.class.php");

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
    <title>Vega Scaffold</title>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="../css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="http://trifacta.github.io/vega/lib/d3.v3.min.js"></script>
    <script src="http://trifacta.github.io/vega/lib/d3.geo.projection.min.js"></script>
    <script src="vega.min.js"></script>
  </head>

  <body>
    <div class="container">
      <div class="row">
        <h1>Ben's Test Visualisation</h>
      </div>
      <div class="row">
        <div role="form" class="form-inline">
          <div class="form-group">
            <label for="xName">Ordinal Data</label>
            <select name="xName" id="xName" onchange="updateChart()" class="form-control">
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
            <label for="yName">Continuous Data</label>
            <select name="yName" id="yName" onchange="updateChart()" class="form-control">
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
            <label for="yMax">Y Max</label>
            <input name="yMax" id="yMax" type="text" value="50000" onchange="updateChart()" class="form-control">
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div id="vis"></div>
      </div>
    </div>
  </body>
  <script type="text/javascript">
  // parse a spec and create a visualization view
  function parse(spec) {
    vg.parse.spec(spec, function(chart) { chart({el:"#vis"}).update(); });
  }

  function barspec(xName, yName, yMax) {
      return ({
        "width": 1000,
        "height": 500,
        "data": [
          {
            "name": "table",
                "url": "vegaproxy.php?dataset="+<?php echo $dataset; ?>+"&fields=[%22"+xName+"%22,%22"+yName+"%22]"
          }
        ],
        "scales": [
          {
            "name": "x",
            "type": "ordinal",
            "range": "width",
            "domain": {"data": "table", "field": "data."+xName}
          },
          {
            "name": "y",
            "range": "height",
            "nice": true,
            "domainMax": yMax,
            "domain": {"data": "table", "field": "data."+yName}
          }
        ],
        "axes": [
          {"type": "x", "scale": "x"},
          {"type": "y", "scale": "y"}
        ],
        "marks": [
          {
            "type": "rect",
            "from": {"data": "table"},
            "properties": {
              "enter": {
                "x": {"scale": "x", "field": "data."+xName},
                "width": {"scale": "x", "band": true, "offset": -1},
                "y": {"scale": "y", "field": "data."+yName},
                "y2": {"scale": "y", "value": 0}
              },
              "update": {
                "fill": {"value": "steelblue"}
              },
              "hover": {
                "fill": {"value": "red"}
              }
            }
          }
        ]
      });
  }

  updateChart()

  function updateChart() {
    parse(barspec(document.getElementById("xName").value, document.getElementById("yName").value, document.getElementById("yMax").value));
  }
  </script>
</html>
