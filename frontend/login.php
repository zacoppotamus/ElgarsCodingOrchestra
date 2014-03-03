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
        <p>Please insert your API key</p>
        <form action="account.php" method="post">
            <input type="text" placeholder="API Key" name="apiKey" autofocus>
            <input type="submit" value="Submit" name="btnSubmit">
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
    </body>
</html>
