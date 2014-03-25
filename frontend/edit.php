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
$user        = $rainhawk->ping()["mashape_user"];
$fields      = $datasetInfo["fields"];

?>
<!doctype html>
<html lang="en-gb">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link href="js/jtable.2.3.1/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="js/jtable.2.3.1/jquery.jtable.js"></script>
        <script src="js/bootstrap.js"></script>
        <title>Our Datasets</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1><?php echo $datasetInfo["name"];?></h>
            </div>
            <div class="row">
                <h3><?php echo $datasetInfo["description"];?></h>
                <a href="account.php" type="button" class="btn btn-warning pull-right"><i class="fa fa-bars"></i>&nbsp Datasets</a>
                <a href='upload.php?dataset=<?php echo $dataset; ?>' class='btn btn-primary pull-right'><i class='fa fa-cloud-upload'></i>&nbsp Upload</a>
                <div class='dropdown pull-right'>
                    <a class='dropdown-toggle btn btn-success' role='button' data-toggle='dropdown' href='#'>
                        <i class='fa fa-bar-chart-o'></i>&nbsp Visualise&nbsp <span class='caret'></span>
                    </a>
                    <ul class='dropdown-menu' role='menu'>
                    <li><a href='barchart.php?dataset=<?php echo $dataset; ?>'>Bar Chart</a></li>
                        <li><a href='piechart.php?dataset=<?php echo $dataset; ?>'>Pie Chart</a></li>
                        <li><a href='scatterchart.php?dataset=<?php echo $dataset; ?>'>Scatter Chart</a></li>
                        <li><a href='areachart.php?dataset=<?php echo $dataset; ?>'>Area Chart</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <?php
                if ($datasetInfo["rows"] !== 0)
                {
                    echo "<div id='dataTable'></div>";
                }
                else
                {
                    echo "<div class='alert alert-info'>".
                            "<strong>No data!</strong> There's no data here. Why don't you try ".
                            "<a class='alert-link' href='upload.php?$dataset'>uploading</a> some?".
                        "</div>";
                }
                ?>
            </div>
            <script>
                $(document).ready(function() {
                        $('#dataTable').jtable({
                            title: 'Data',
                            paging: true,
                            pageSize: 50,
                            sorting: true,
                            defaultSorting: 'name ASC',
                            actions: {
                                listAction: 'proxy/list.php?dataset=<?php echo $dataset; ?>',
                                <?php
                                if(in_array($user, $datasetInfo["write_access"]) && $datasetInfo["rows"] != 0)
                                {
                                    echo("createAction: 'proxy/create.php?dataset=$dataset',"
                                       . "updateAction: 'proxy/update.php?dataset=$dataset',"
                                       . "deleteAction: 'proxy/delete.php?dataset=$dataset'");
                                }
                                ?>
                            },
                            fields: {
                                <?php
                                    $colwidth = 100/count($fields);
                                    for($i=0; $i<count($fields); $i++)
                                    {
                                        if($fields[$i] !== "_id")
                                        {
                                            echo ("'$fields[$i]': {title:'$fields[$i]', width:'$colwidth%'}, ");
                                        }
                                    }
                                ?>
                                _id: {
                                    key: true,
                                    create: false,
                                    edit: false,
                                    list: false
                                }
                            }
                        });
                        $('#dataTable').jtable('load');
                });
            </script>
    </body>
</html>

