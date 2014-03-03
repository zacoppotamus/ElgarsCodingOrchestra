<?php
if(isset($_COOKIE["apiKey"]))
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
        <link rel="stylesheet" href="../css/bootstrap.css">
    </head>
    <body>
        <div class="container">
            <p>Please insert your API key</p>
            <form action="account.php" role = form method="post">
                <div class="form-group">
                    <label for="apiKey">API Key</label>
                    <input type="text" placeholder="API Key" name="apiKey" autofocus>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
            <?php
            if(isset($_GET["fail"]))
            {
               echo "<p>Incorrect API Key.</p>";
            }
            elseif(isset($_GET["logout"]))
            {
                setcookie(apiKey, "", time()-3600, "/", "project.spe.sneeza.me", isset($_SERVER["HTTPS"]), false);
                echo "<p>Successfully logged out.</p>";
            }
            ?>
        </div>
    </body>
</html>
