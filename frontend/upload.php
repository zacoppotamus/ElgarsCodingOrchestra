<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$datasetInfo = $rainhawk->fetchDataset($dataset);

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Upload Data</title>
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

            var file;

            $(function() {
                $('form').submit(uploadDataset);
                $('input[type=file]').change(prepareUpload);
            });

            function prepareUpload(event) {
                file = event.target.files[0];
            }

            function verifyDataset(name, success) {
                var url = 'https://sneeza-eco.p.mashape.com/datasets/' + name;

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        if(data.meta.code === 200) {
                            success();
                        } else {
                            errormsg("Dataset does not exist or you do not have write access. Try creating a dataset using the <a class='alert-link' href='create.php'>create</a> interface.")
                        }
                    },
                    error: function(data) {
                        return false;
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("X-Mashape-Authorization", "<?php echo $mashape_key; ?>");
                    }
                });
            }

            function uploadDataset(event) {
                if($('#datasetName').length) {
                    var name = $('#datasetName').val();
                } else {
                    name = '<?php echo $dataset ?>';
                }

                verifyDataset(name, function() {
                    $('#btnSubmit').button('loading');

                    var type = $('#datasetType').val();
                    var url = 'https://sneeza-eco.p.mashape.com/datasets/' + name + "/upload/" + type;

                    $.ajax({
                        url: url,
                        type: 'PUT',
                        processData: false,
                        contentType: false,
                        data: file,
                        datatype: 'json',
                        success: function(data) {
                            if(data.meta.code === 200) {
                                successmsg(name);
                            } else {
                                errormsg(data.data.message);
                            }
                        },
                        error: function(err) {
                            errormsg(JSON.stringify(err));
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader("X-Mashape-Authorization", "<?php echo $mashape_key; ?>");
                        }
                    });
                });

                return false;
            }

            function errormsg(message) {
                $(".container:last").prepend(
                    "<div class='alert alert-danger fade in'>"+
                        "<strong>Error!</strong> " + message +
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
                    "</div>");

                $('#btnSubmit').button('reset');
            }

            function successmsg(name) {
                $(".container:last").prepend(
                    "<div class='alert alert-success fade in'>"+
                        "<strong>Done!</strong> Data successfully uploaded to dataset <a class='alert-link' href='properties.php?dataset="+name+"'>"+name+"</a>. "+
                        "Now try <a class='alert-link' href='newlogic/?dataset="+name+"'>visualising</a> the data."+
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
                    "</div>");

                $('#btnSubmit').button('reset');
            }
        </script>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1>Upload data!</h1>
                            <p>Select a file to upload into the relevant dataset...</p>
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
                                    <?php if(isset($dataset)) { ?>
                                        <p class="form-control-static"><?php echo $dataset; ?></p>
                                    <?php } else { ?>
                                        <input type="text" class="form-control" id="datasetName" name="datasetName" placeholder='Enter your dataset name here...' required autofocus>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label for="datasetFile">Type:</label>
                                    <select class="form-control" id="datasetType">
                                        <option value="csv">csv</option>
                                        <option value="xlsx">xlsx</option>
                                        <option value="ods">ods</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="datasetFile">File</label>
                                    <input type="file" id="datasetFile" name="datasetFile">
                                </div>
                                <div class="form-controls">
                                    <button id="btnSubmit" type="submit" data-loading-text="Uploading..." class="btn btn-default">Upload</button>
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