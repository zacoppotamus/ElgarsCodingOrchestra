<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$datasetsInfo = $rainhawk->listDatasets();

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Datasets</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
        <script type="text/javascript">
            $(function() {
                $(".confirm").confirm({
                    text: "Are you sure you wish to delete this dataset? This action is irreversible!",
                    title: "Really?",
                    confirmButton: "Delete"
                });
            });
        </script>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <?php if(isset($_GET['deletefailed'])) { ?>
                <div class="row">
                    <div class="alert alert-danger fade in">
                        <strong>Error!</strong> Deletion failed
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    </div>
                </div>
            <?php } else if(isset($_GET['deleted'])) { ?>
                <div class="row">
                    <div class="alert alert-success fade in">
                        <strong>Gone!</strong> Dataset successfully deleted.
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    </div>
                </div>
            <?php } ?>
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
                        <?php foreach($datasetsInfo as $dataset) { ?>
                            <tr>
                                <td><a href="/properties.php?dataset=<?php echo $dataset['name']; ?>"><?php echo $dataset['name']; ?></a></td>
                                <td><?php echo $dataset['description']; ?></td>
                                <td><?php echo $dataset['rows']; ?></td>
                                <td><?php echo count($dataset['fields']); ?></td>
                                <td><?php echo in_array($user, $dataset['write_access']) ? "Write" : "Read"; ?></td>
                                <td>
                                    <a href="/edit.php?dataset=<?php echo $dataset['name']; ?>" class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i>&nbsp; View
                                    </a>
                                </td>
                                <td>
                                    <a href="/visualise/?dataset=<?php echo $dataset['name']; ?>" class="btn btn-success btn-sm" <?php echo $dataset['rows'] == 0 ? "disabled" : null; ?>>
                                        <i class="fa fa-bar-chart-o"></i>&nbsp; Visualise
                                    </a>
                                </td>
                                <td>
                                    <a href="/upload.php?dataset=<?php echo $dataset['name']; ?>" class="btn btn-primary btn-sm" <?php echo !in_array($user, $dataset['write_access']) ? "disabled" : null; ?>>
                                        <i class="fa fa-cloud-upload"></i>&nbsp; Upload
                                    </a>
                                </td>
                                <td>
                                    <a href="/proxy/delete.php?dataset=<?php echo $dataset['name']; ?>" id="del<?php echo explode(".", $dataset['name'])[1]; ?>" class="btn btn-danger btn-sm confirm" <?php echo !in_array($user, $dataset['write_access']) ? "disabled" : null; ?>>
                                        <i class="fa fa-ban"></i>&nbsp; Delete
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
