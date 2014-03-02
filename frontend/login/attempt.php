<?php
$mashape_key = $_POST["apiKey"];

$ch = curl_init();

$url = "https://sneeza-eco.p.mashape.com/datasets";

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "ECO / Login System 0.1");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $mashape_key));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = json_decode(curl_exec($ch), true);
curl_close($ch);

if (stristr($result["message"], "Invalid Mashape key"))
{
    header('Location: index.html');
}
?>

<!DOCTYPE html>
<html lan="en-GB">
    <head>
        <meta charset="UTF-8">
        <title>Account Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="../css/bootstrap.css">

        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1>Welcome %accountname%</h1>
                <h2>Please pick a dataset to view/edit</h>
            </div>
            <div class="row">
                <table class="table">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Records</th>
                    <th>Fields</th>
                    <th>Read Access</th>
                    <th>Write Access</th>
                </table>
            </div>
    </body>
</html>
