<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$datasetsInfo = $rainhawk->datasets();
$user         = $rainhawk->ping()["mashape_user"];

if ($user == false)
{
    header('Location: login.php?fail');
    exit();
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
        <script src="js/bootstrap.js"></script>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1>Hello <?php echo $user; ?></h1>
                <h3>Please pick a dataset to view, edit or visualise</h>
                <a href="login.php?logout" type="button" class="btn btn-warning pull-right">Logout</a>
            </div>
            <div class="row">
                <?php if(isset($_GET["deletefailed"]))
                {
                    echo "<div class='alert alert-danger fade in'>".
                            "<strong>Error!</strong> Deletion failed".
                            "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>".
                        "</div>";
                }?>
                <?php if(isset($_GET["deleted"]))
                {
                    echo "<div class='alert alert-success fade in'>".
                            "<strong>Deleted</strong> Successful Deletion".
                            "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>".
                        "</div>";
                }?>
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
                                "<td>".
                                    "<div class='dropdown'>".
                                        "<a class='dropdown-toggle btn btn-success btn-sm' role='button' data-toggle='dropdown' href='#'>".
                                            "Visualise <span class='caret'></span>".
                                        "</a>".
                                        "<ul class='dropdown-menu' role='menu'>".
                                            "<li><a href='barchart.php?dataset=$dataset[name]'>Bar Chart</a></li>".
                                            "<li><a href='piechart.php?dataset=$dataset[name]'>Pie Chart</a></li>".
                                            "<li><a href='scatterchart.php?dataset=$dataset[name]'>Scatter Chart</a></li>".
                                            "<li><a href='areachart.php?dataset=$dataset[name]'>Area Chart</a></li>".
                                        "</ul>".
                                    "</div>".
                                "</td>".
                                "<td><a href='upload.php?dataset=$dataset[name]' class='btn btn-primary btn-sm'>Upload</a></td>".
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
