<?php

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

function getRequest($requestURL, $auth_key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "ECO / Login System 0.1");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $auth_key));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result=json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

$url          = "https://sneeza-eco.p.mashape.com/";
$datasetsInfo = getRequest($url."datasets", $mashape_key);
$user         = getRequest($url."ping",     $mashape_key)["data"]["mashape_user"];

if (stristr($datasetsInfo["message"], "Invalid Mashape key"))
{
    header('Location: login.php?fail');
}
else
{
    // Could not set httponly to true as the cookie is used in the upload
    // feature
    setcookie(apiKey, $mashape_key, 0, "/", "project.spe.sneeza.me", isset($_SERVER["HTTPS"]), false);
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
            <h1>Welcome <?php echo $user; ?></h1>
                <h2>Please pick a dataset to view/edit</h>
            </div>
            <div class="row">
                <table class="table">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Records</th>
                        <th>Fields</th>
                        <th>Read Access</th>
                        <th>Write Access</th>
                    </tr>

                    <?php
                    for($i=0; $i<count($datasetsInfo["data"]["datasets"]); $i++)
                    {
                        $dataset = $datasetsInfo["data"]["datasets"][$i];
                        echo("<tr>\n".
                            "<td><a href='edit.php?dataset=$dataset[name]'>$dataset[name]</a></td>\n".
                            "<td>$dataset[description]</td>\n".
                            "<td>$dataset[rows]</td>\n".
                            "<td>".count($dataset[fields])."</td>\n".
                            "<td>".(in_array($user, $dataset["read_access" ]) ? "True" : "False")."</td>\n".
                            "<td>".(in_array($user, $dataset["write_access"]) ? "True" : "False")."</td>\n".
                            "</tr>\n");
                    }

                    ?>
                    <tr colspan='0'><a href='upload.html'>Upload your own data</a></tr>
                </table>
            </div>
    </body>
</html>
