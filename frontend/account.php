<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$datasetsInfo = $rainhawk->listDatasets();

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user)
{
    header('Location: login.php?dest='.urlencode($_SERVER['REQUEST_URI']));
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
        <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>

        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.confirm.min.js"></script>
        <script>
            $(document).ready(function(){
                $(".confirm").confirm({
                    text: "Are you sure you wish to delete this dataset? This action is irreversible",
                    title: "Really delete?",
                    confirmButton: "Delete"
                });
            });
        </script>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1>Hello <?php echo $user; ?></h1>
                <h3>Please pick a dataset to view, edit or visualise</h>
                <a href="login.php?logout" type="button" class="btn btn-warning pull-right"><i class="fa fa-sign-out"></i>&nbsp Logout</a>
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
                            "<strong>Gone!</strong> Dataset successfully deleted.".
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
                            <th>Access</th>
                            <th></th>
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
                                "<td><a href='properties.php?dataset=$dataset[name]'>$dataset[name]</a></td>\n".
                                "<td>$dataset[description]</td>\n".
                                "<td>$dataset[rows]</td>\n".
                                "<td>".count($dataset['fields'])."</td>\n".
                                //"<td>".(in_array($user, $dataset["read_access" ]) ? "True" : "False")."</td>\n".
                                "<td>".(in_array($user, $dataset["write_access"]) ? "Write" : "Read")."</td>\n".
                                "<td>".
                                    "<a href='edit.php?dataset=$dataset[name]' class='btn btn-info btn-sm'>".
                                    "<i class='fa fa-edit'></i>&nbsp; View</a>".
                                "</td>".
                                "<td>".
                                    "<a class='btn btn-success btn-sm' href='newlogic/?dataset=$dataset[name]' ".
                                        (($dataset["rows"] == 0) ? "disabled>" : ">") .
                                            "<i class='fa fa-bar-chart-o'></i>&nbsp; Visualise".
                                    "</a>".
                                "</td>".
                                "<td>".
                                    "<a href='upload.php?dataset=$dataset[name]' class='btn btn-primary btn-sm'".(in_array($user, $dataset["write_access"]) ? "" : "disabled").">".
                                    "<i class='fa fa-cloud-upload'></i>&nbsp; Upload</a>".
                                "</td>".
                                "<td><a href='delete.php?dataset=$dataset[name]' id='del".explode(".", $dataset["name"])[1].
                                    "' class='btn btn-danger btn-sm confirm'".(in_array($user, $dataset["write_access"]) ? "" : "disabled")."><i class='fa fa-ban'></i>&nbsp Delete</a></td>".
                                "</tr>\n");
                        }
                        ?>
                    <tbody>
                    <tfoot>
                        <tr><td colspan='100%'><p class="text-center"><strong><a href='create.php'>Create a dataset</a></strong></p></td>
                    </tfoot>
                </table>
            </div>
    </body>
</html>
