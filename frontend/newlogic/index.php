<?php
require_once("../../wrappers/php/rainhawk.class.php");

session_start();

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;
?>

<!DOCTYPE html>
<html ng-app="eco">
    <head>
        <meta charset="utf-8">
        <title>AngularJS + D3.js</title>
        <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style/charts.css">
        <link rel="stylesheet" href="style/style.css">
        <base href="/newlogic/">
    </head>
    <!-- Remove ng-controller here as it is injected in js/app.js and shouldn't be injected twice-->
    <body>

        <div id="main">
            <div ng-view></div>
        </div>

        <!-- <div id="vegavis"></div> -->

        <script>
          var apiKey = <?php echo $mashape_key; ?>;
        </script>
        <script src="vendor/jquery/dist/jquery.min.js"></script>
        <script src="vendor/angular/angular.min.js"></script>
        <script src="vendor/angular-bootstrap/ui-bootstrap.js"></script>
        <script src="vendor/angular-route/angular-route.min.js"></script>
        <script src="vendor/d3/d3.min.js"></script>
        <script src="vendor/vega/vega.min.js"></script>
        <script src="vendor/three.js/src/three.min.js"></script>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <script src="js/app.js"></script>
        <!-- ECO -->
        <script src="js/eco.charts.js"></script>
        <script src="js/controllers.js"></script>
        <script src="charts/barchart.js"></script>
        <script src="charts/d3barchart.js"></script>
        <script src="charts/d3piechart.js"></script>
        <script src="charts/d3map.js"></script>
        <script src="charts/d3bubblechart.js"></script>
        <script src="charts/d3treemap.js"></script>
        <script src="charts/threetest.js"></script>
        <script src="js/directives.js"></script>
        <script src="js/services.js"></script>
    </body>
</html>