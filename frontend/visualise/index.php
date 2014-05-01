<?php

require_once "../includes/core.php";
require_once "../includes/check_login.php";

?>
<!DOCTYPE html>
<html lang="en-GB" ng-app="eco">
    <head>
        <title>Project Rainhawk - Visualise</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "../includes/meta.php"; ?>
        <link rel="stylesheet" href="style/charts.css">
        <link rel="stylesheet" href="style/style.css">
        <base href="/visualise/">
    </head>

    <!-- Remove ng-controller here as it is injected in js/app.js and shouldn't be injected twice-->
    <body>
        <?php

        ob_start();
        require_once "../includes/nav.php";
        $nav = ob_get_clean();
        ob_end_flush();

        // We have to set the links to target the root page instead of Angular.
        echo str_replace('href=', 'target="_self" href=', $nav);

        ?>

        <div id="main">
            <div ng-view></div>
        </div>

        <script>
            var apiKey = "<?php echo $mashape_key; ?>";
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
        <script src="charts/d3barchart.js"></script>
        <script src="charts/d3piechart.js"></script>
        <script src="charts/d3map.js"></script>
        <script src="charts/d3bubblechart.js"></script>
        <script src="charts/d3treemap.js"></script>
        <script src="charts/glscatter.js"></script>
        <script src="js/directives.js"></script>
        <script src="js/services.js"></script>
    </body>
</html>
