<?php

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link href="jtable.2.3.1/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css">
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="js/jtable.2.3.1/jquery.jtable.js"></script>
        <title>Our Datasets</title>
    </head>
    <body>
        <div id="dataTable"></div>
        <script>
            $(document).ready(function() {
                    $('#dataTable').jtable({
                        title: 'Data Table',
                        actions: {
                            listAction: 'http://project.spe.sneeza.me/proxy/list.php?dataset=<?php echo $dataset; ?>',
                            createAction: 'http://project.spe.sneeza.me/proxy/create.php?dataset=<?php echo $dataset; ?>',
                            updateAction: 'http://project.spe.sneeza.me/proxy/update.php?dataset=<?php echo $dataset; ?>',
                            deleteAction: 'http://project.spe.sneeza.me/proxy/delete.php?dataset=<?php echo $dataset; ?>'
                        },
                        fields: {
                            _id: {
                                key: true,
                                create: false,
                                edit: false,
                                list: false
                            },
                            name: {
                                title: "Name"
                            },
                            lines: {
                                title: "Lines"
                            },
                            type: {
                                title: "Type"
                            },
                            latitude: {
                                title: "Lat"
                            },
                            longitude: {
                                title: "Lon"
                            }
                        }
                    });
                    $('#dataTable').jtable('load');
            });
        </script>
    </body>
</html>

