<?php
session_start();

if(isset($_SESSION["apiKey"]) && !isset($_GET["logout"]))
{
    header("Location: account.php");
}
?>
<html lan="en-GB">
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Bootstrap -->
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1>Login to Project Rainhawk</h>
                <a href="/" class="btn btn-warning pull-right"><i class="fa fa-home"></i> &nbspHome</a>
            </div>
            <div class="row">
                <p>Please insert your API key</p>
                <form action="account.php" role="form" method="post">
                    <div class="form-group<?php if(isset($_GET["fail"])){echo " has-warning";}?>">
                        <label for="apiKey">API Key</label>
                        <input type="text" placeholder="API Key" name="apiKey" class="form-control" autofocus>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
                <?php
                if(isset($_GET["fail"]))
                {
                   echo "<p class='text-danger'>Incorrect API Key.</p>";
                }
                elseif(isset($_GET["logout"]))
                {
                    session_unset();
                    echo "<p class='text-success'>Successfully logged out.</p>";
                }
                ?>
            </div>
            <div class='row'>
                <h3><i class='fa fa-key'></i> Don't have an API key?</h3>
                <p>Register an API key for Project Rainhawk at <a href='https://www.mashape.com/sneeza/project-rainhawk'>Mashape.com</a></p>
            </div>
        </div>
    </body>
</html>
