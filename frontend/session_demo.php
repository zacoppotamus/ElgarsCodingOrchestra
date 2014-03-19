<?php

session_start();

if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$api_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

if(!isset($api_key)) {
    // Blah.
}

// Unset apiKey is simple as.
// unset($_SESSION['apiKey']);

?>
<html lan="en-GB">
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Bootstrap -->
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="../css/bootstrap.css">
    </head>
    <body>
        <div class="container">
            <?php

            if(isset($api_key)) {
                ?>
                <div class="row">
                    <p>API Key: <?php echo $api_key; ?></p>
                </div>
                <?php
            }

            ?>
            <div class="row">
                <h1>Login to Project Rainhawk</h>
                <a href="/" class="btn btn-warning pull-right">Home</a>
            </div>
            <div class="row">
                <p>Please insert your API key</p>
                <form action="session_demo.php" role="form" method="post">
                    <div class="form-group">
                        <label for="apiKey">API Key</label>
                        <input type="text" placeholder="API Key" name="apiKey" class="form-control" autofocus>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </body>
</html>
