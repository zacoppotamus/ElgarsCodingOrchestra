<html>
    <head>
        <title>Login</title>
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
            setcookie("apiKey", "", time()-3600);
           echo "<p>Successfully logged out.</p>";
        }
        ?>
    </body>
</html>
