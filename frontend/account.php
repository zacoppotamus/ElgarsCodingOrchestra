<?php
require_once("../wrappers/php/rainhawk.class.php");

$mashape_key = isset($_POST["apiKey"]) ? $_POST["apiKey"] : $_COOKIE["apiKey"];

$rainhawk = new Rainhawk($mashape_key);

$datasetsInfo = $rainhawk->datasets();
$user         = $rainhawk->ping()["mashape_user"];

if ($user == false)
{
    header('Location: login.php?fail');
    exit();
}
else
{
    setcookie("apiKey", $mashape_key, 0, "/");
}
?>

<!DOCTYPE html>
<html lan="en-GB">
    <head>
        <title>Account Page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/bootstrap.css">

        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1>Hello <?php echo $user; ?></h1>
                <h3>Please pick a dataset to view, edit or visualise</h>
                <a href="login.php?logout" type="button" class="btn btn-warning pull-right">Logout</a>
            </div>
            <div class="row">
                <?php if(isset($_GET["deletefailed"])){echo "<div class='alert alert-danger'><strong>Error!</strong> Deletion failed</div>";}?>
                <?php if(isset($_GET["deleted"])){echo "<div class='alert alert-success'><strong>Deleted</strong> Successful Deletion</div>";}?>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Records</th>
                            <th>Fields</th>
                            <th>Write Access</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </head>
                    <tbody>
                        <?php
                        for($i=0; $i<count($datasetsInfo); $i++)
                        {
                            $dataset = $datasetsInfo[$i];
                            echo("<tr>\n".
                                "<td><a href='edit.php?dataset=$dataset[name]'>$dataset[name]</a></td>\n".
                                "<td>$dataset[description]</td>\n".
                                "<td>$dataset[rows]</td>\n".
                                "<td>".count($dataset['fields'])."</td>\n".
                                //"<td>".(in_array($user, $dataset["read_access" ]) ? "True" : "False")."</td>\n".
                                "<td>".(in_array($user, $dataset["write_access"]) ? "True" : "False")."</td>\n".
                                "<td><a href='vega-test/vegatest.php?dataset=$dataset[name]' class='btn btn-success btn-sm'>Visualise</a></td>".
                                "<td><a href='delete.php?dataset=$dataset[name]' class='btn btn-danger btn-sm'>Delete</a></td>".
                                "</tr>\n");
                        }

                        ?>
                    <tbody>
                    <tfoot>
                        <tr><td colspan='100%'><p class="text-center"><strong><a href='create.php'>Create a dataset</a></strong></p></td>
                        <tr><td colspan='100%'><p class="text-center"><strong><a href='upload.php'>Upload your own data</a></strong></p></td></tr>
                    </tfoot>
                </table>
            </div>
    </body>
</html>
