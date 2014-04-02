<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

$mashape_key = isset($_SESSION['apiKey']) ? trim($_SESSION['apiKey']) : null;

$rainhawk = new Rainhawk($mashape_key);

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user)
{
    header('Location: login.php?dest='.urlencode($_SERVER['REQUEST_URI']));
    exit();
}

?>

<html>
  <head>
    <title>Upload</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>

    <!-- jQuery -->
    <script src="js/jquery-1.10.2.js"></script>

    <!-- Bootstrap Plugins -->
    <script src="js/bootstrap.js"></script>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <h1>Upload</h1>
        <h3>
          Upload a data file
          <?php
            if(isset($_GET["dataset"]))
            {
              require_once("helpers/datasetButtons.php");
              echo navButtons($_GET["dataset"], true);
            }
            else
            {
              echo <<<EOD
                <a href="account.php" type="button" class="btn btn-warning pull-right">
                    <i class="fa fa-bars"></i>&nbsp Datasets
                </a>
EOD;
            }
          ?>
        </h3>
      </div>
      <div class="row">
        <form role="form">
          <div class="form-group">
            <label for="datasetName">Dataset Name</label>
            <?php if(isset($_GET["dataset"])){
              echo "<p class='form-control-static'>$_GET[dataset]</p>";
            }
            else
            {
              echo "<input type='text' class='form-control' id='datasetName'".
                   "name='datasetName' placeholder='Dataset Name' required autofocus>";
            }?>
          </div>
          <div class="form-group">
            <label for="datasetFile">Type</label>
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
          <button id='btnSubmit' type="submit" data-loading-text='Uploading...' class="btn btn-default">Submit</button>
        </form>
      </div>
    </div>
<script>
var file;

$('form').submit(uploadDataset);
$('input[type=file]').change(prepareUpload);

function prepareUpload(event)
{
  file = event.target.files[0];
}

function verifyDataset(name, success)
{
  var url = 'https://sneeza-eco.p.mashape.com/datasets/' + name;

  $.ajax({
    url: url,
    type: "GET",
    success: function(data){
      if(data.meta.code === 200)
      {
        success();
      }
      else
      {
        errormsg("Dataset does not exist or you do not have write access. "+
        "Try creating a dataset using the <a class='alert-link' href='create.php'>create</a> interface.")
      }
    },
    error: function(data){return false;},
    beforeSend: function(xhr) {
      xhr.setRequestHeader("X-Mashape-Authorization", "<?php echo $mashape_key; ?>");
    }
  });

}

function uploadDataset(event)
{
  event.stopPropagation();
  event.preventDefault();

  if($('#datasetName').length)
  {
    var name = $('#datasetName').val();
  }
  else
  {
    name = '<?php echo $_GET["dataset"]; ?>';
  }

  verifyDataset(name, function(){

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
        if(data.meta.code === 200)
        {
          successmsg(name);
        }
        else
        {
          errormsg(data.data.message);
        }
      },
      error: function(err) { errormsg(JSON.stringify(err)); },
      beforeSend: function(xhr) {
        xhr.setRequestHeader("X-Mashape-Authorization", "<?php echo $mashape_key; ?>");
      }
    });

  });


  return false;
}

function errormsg(message)
{
  $("form").prepend(
    "<div class='alert alert-danger fade in'>"+
      "<strong>Error!</strong> " + message +
      "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
    "</div>");

    // Reset upload button
    $('#btnSubmit').button('reset');
}

function successmsg(name)
{
  $("form").prepend(
    "<div class='alert alert-success fade in'>"+
      "<strong>Done!</strong> Data successfully uploaded to dataset <a class='alert-link' href='properties.php?dataset="+name+"'>"+name+"</a>. "+
      "Now try <a class='alert-link' href='newlogic/?dataset="+name+">visualising</a> the data."+
      "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
    "</div>");

    // Reset upload button
    $('#btnSubmit').button('reset');
}

</script>
  </body>
</html>

