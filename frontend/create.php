<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Create Dataset</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
        <style type="text/css">
            .row .form-controls {
                margin-top: 40px;
            }
        </style>
        <script>
            rainhawk.apiKey = "<?php echo $mashape_key; ?>";

            $(function() {
                $("form").submit(function(e) {
                    var $submit = $(this).find("input[type=submit]");
                    var dataset = $("#datasetName").val();
                    var description = $("#datasetDescription").val();

                    $submit.attr("disabled", true);

                    rainhawk.datasets.create(dataset, description, function(data) {
                        $(".container:last").prepend($('<div class="alert alert-success fade in"></div>')
                            .append($('<span><strong>Success!</strong> Dataset <a class="alert-link" href="/edit.php?dataset=' + data.name + '">' + data.name + '</a> successfully created.&nbsp;</span>'))
                            .append($('<span>Now try <a class="alert-link" href="/upload.php?dataset=' + data.name + '">uploading</a> some data.</span>'))
                            .append($('<button type="button" class="close pull-right" data-dismiss="alert" aria-hidden="true">&times;</button>'))
                        );
                    }, function(message) {
                        $(".container:last").prepend($('<div class="alert alert-danger fade in"></div>')
                            .append($('<strong>Error!</strong>&nbsp;'))
                            .append(message)
                            .append($('<button type="button" class="close pull-right" data-dismiss="alert" aria-hidden="true">&times;</button>'))
                        );

                        $submit.removeAttr("disabled");
                    });

                    return false;
                });
            });
        </script>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1>Create a new dataset...</h1>
                            <p>Get started here by entering a unique name for your dataset along with a description for what this dataset will hold.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form role="form">
                                <div class="form-group">
                                    <label for="datasetName">Dataset Name:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $user; ?>.</span>
                                        <input type="text" class="form-control" id="datasetName" name="datasetName" placeholder="Enter the name of the dataset..." required autofocus>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="datasetDescription">Dataset Description:</label>
                                    <input type="text" class="form-control" id="datasetDescription" name="datasetDescription" placeholder="Enter a description for this dataset..." required>
                                </div>
                                <div class="form-controls">
                                    <button type="submit" class="btn btn-default">Submit</button>
                                    <a href="/datasets.php" type="button" class="btn btn-danger">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
