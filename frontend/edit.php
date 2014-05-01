<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$datasetInfo = $rainhawk->fetchDataset($dataset);
$fields = $datasetInfo['fields'];
$colWidth = 100 / count($fields);

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Edit Data</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
        <link rel="stylesheet" href="/js/jtable.2.3.1/themes/metro/blue/jtable.min.css" type="text/css">
        <script src="/js/jtable.2.3.1/jquery.jtable.js" type="text/javascript"></script>
        <style type="text/css">
            #dataTable {
                margin-left: 15px;
                margin-right: 15px;
                border-radius: 6px;
                overflow: hidden;
            }
        </style>
        <script type="text/javascript">
            $(function() {
                $("#dataTable").jtable({
                    title: "Data",
                    paging: true,
                    pageSize: 50,
                    sorting: true,
                    defaultSorting: "name ASC",
                    actions: {
                        listAction: '/proxy/list_data.php?dataset=<?php echo $dataset; ?>',
                        <?php if(in_array($user, $datasetInfo['write_access']) && $datasetInfo['rows'] > 0) { ?>
                            createAction: '/proxy/insert_data.php?dataset=<?php echo $dataset; ?>',
                            updateAction: '/proxy/update_data.php?dataset=<?php echo $dataset; ?>',
                            deleteAction: '/proxy/delete_data.php?dataset=<?php echo $dataset; ?>'
                        <?php } ?>
                    },
                    fields: {
                        <?php foreach($fields as $field) { ?>
                            <?php if($field == "_id") continue; ?>
                            '<?php echo $field; ?>': {
                                title: '<?php echo $field; ?>',
                                width: '<?php echo $colWidth; ?>%'
                            },
                        <?php } ?>
                        _id: {
                            key: true,
                            create: false,
                            edit: false,
                            list: false
                        }
                    }
                }).jtable("load");
            });
        </script>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1><?php echo $datasetInfo['name'];?></h1>
                            <p><?php echo $datasetInfo['description'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php if($datasetInfo['rows'] > 0) { ?>
                    <div id="dataTable"></div>
                <?php } else { ?>
                    <div class="alert alert-info">
                        <strong>No data!</strong> There's no data here. Why don't you try <a class="alert-link" href="/upload.php?dataset=<?php echo $datasetInfo['name']; ?>">uploading</a> some?
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>