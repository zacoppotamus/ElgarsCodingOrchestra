<?php

require_once "includes/core.php";

if(isset($_POST['apiKey'])) {
    $mashape_key = trim($_POST['apiKey']);
    $rainhawk = new Rainhawk($mashape_key);
    $ping = $rainhawk->ping();
    $user = null;

    if($ping) {
        $user = $ping['mashape_user'];
    }

    if(empty($user)) {
        header("Location: /login.php?fail");
        exit;
    } else {
        $location = isset($_GET['dest']) ? urldecode($_GET['dest']) : "/datasets.php";

        $_SESSION['apiKey'] = $mashape_key;
        $_SESSION['user'] = $user;

        header("Location: " . $location);
        exit;
    }
}

if(isset($_SESSION['apiKey']) && isset($_SESSION['user']) && !isset($_GET['logout'])) {
    header("Location: /datasets.php");
    exit;
}

if(isset($_GET['logout'])) {
    session_unset();
}

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <?php if(isset($_GET['dest'])) { ?>
                <div class="alert alert-danger">
                    <p>You need to be logged in to access this page!</p>
                </div>
            <?php } ?>
            <?php if(isset($_GET['fail'])) { ?>
                <div class="alert alert-danger">
                    <p>Your API key could not be validated with the service, please try again.</p>
                </div>
            <?php } ?>
            <?php if(isset($_GET['logout'])) { ?>
                <div class="alert alert-success">
                    <p>Successfully logged out.</p>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1>Login to manage your data!</h1>
                            <p>Please insert your Mashape API key...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form action="login.php<?php echo isset($_GET['dest']) ? "?dest=" . $_GET['dest'] : null; ?>" role="form" method="post">
                                <div class="form-group <?php echo isset($_GET['fail']) ? "has-warning" : null; ?>">
                                    <label for="apiKey">API Key:</label>
                                    <input type="text" placeholder="Enter your API key here..." name="apiKey" class="form-control" autofocus>
                                </div>
                                <button type="submit" class="btn btn-default">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><i class="fa fa-key"></i>&nbsp; Don't have an API key?</h3>
                            <p>Register an API key for Project Rainhawk at <a href="https://www.mashape.com/sneeza/project-rainhawk">Mashape.com</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
