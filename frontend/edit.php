<?php

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$mashape_key = "eSQpirMYxjXUs8xIjjaUo72gutwDJ4CP";

function getRequest($requestURL, $auth_key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "ECO / Edit System 0.5");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $auth_key));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result=json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

$datasetInfo = getRequest("https://sneeza-eco.p.mashape.com/datasets/".$dataset, $mashape_key);
$user = getRequest("https://sneeza-eco.p.mashape.com/ping", $mashape_key)["data"]["mashape_user"];
$fields = $datasetInfo["data"]["fields"];

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link href="js/jtable.2.3.1/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css">
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
                        title: '<?php echo $datasetInfo["data"]["name"];?>',
                        paging: true,
                        pageSize: 50,
                        sorting: true,
                        defaultSorting: 'name ASC',
                        actions: {
                            listAction: 'http://project.spe.sneeza.me/proxy/list.php?dataset=<?php echo $dataset; ?>',
                            <?php
                            if(in_array($user, $datasetInfo["data"]["write_access"]))
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

