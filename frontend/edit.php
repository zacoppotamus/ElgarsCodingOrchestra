<?php
require_once("../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;

$datasetInfo = $rainhawk->fetchDataset($dataset);
$user        = $rainhawk->ping()["mashape_user"];
$fields      = $datasetInfo["fields"];

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link href="js/jtable.2.3.1/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css">
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="js/jtable.2.3.1/jquery.jtable.js"></script>
        <title>Our Datasets</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1><?php echo $datasetInfo["name"];?></h>
                <h3><?php echo $datasetInfo["description"];?></h>
                <a href="account.php" type="button" class="btn btn-warning pull-right">Back</a>
            </div>
            <div class="row">
                <div id="dataTable"></div>
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
                                listAction: 'http://project.spe.sneeza.me/proxy/list.php?dataset=<?php echo $dataset; ?>',
                                <?php
                                if(in_array($user, $datasetInfo["write_access"]))
                                {
                                    echo("createAction: 'http://project.spe.sneeza.me/proxy/create.php?dataset=$dataset',"
                                       . "updateAction: 'http://project.spe.sneeza.me/proxy/update.php?dataset=$dataset',"
                                       . "deleteAction: 'http://project.spe.sneeza.me/proxy/delete.php?dataset=$dataset'");
                                }
                                ?>
                            },
                            fields: {
                                <?php
                                    for($i=0; $i<count($fields); $i++)
                                    {
                                        if($fields[$i] !== "_id")
                                        {
                                            echo ("$fields[$i]: {title:'$fields[$i]'},");
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

