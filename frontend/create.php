<?php
require_once("../wrappers/php/rainhawk.class.php");

session_start();

if(isset($_POST['apiKey'])) {
    $_SESSION['apiKey'] = trim($_POST['apiKey']);
}

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
    <title>Create</title>
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
        <h1>Create</h1>
        <h3>
          Create a new dataset
          <a href="account.php" type="button"
            class="btn btn-warning pull-right">
                <i class="fa fa-bars"></i>&nbsp Datasets
          </a>
        </h3>
      </div>
      <div class="row">
        <form role="form">
          <div class="form-group">
            <label for="datasetName">Dataset Name</label>
            <div class="input-group">
              <span class="input-group-addon"><?php echo $user; ?>.</span>
              <input type="text" class="form-control" id="datasetName"
                name="datasetName" placeholder="Dataset Name" required autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="datasetDescription">Dataset Description</label>
            <input type="text" class="form-control" id="datasetDescription"
              name="datasetDescription" placeholder="Dataset Description" required>
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>
    </div>
<script>
$('form').submit(createDataset);

function createDataset(event)
{
    event.stopPropagation();
    event.preventDefault();

    var postdata = new Object();
    postdata.name = $('#datasetName').val();
    postdata.description = $('#datasetDescription').val();

    $.ajax({
      url: 'https://sneeza-eco.p.mashape.com/datasets',
      type: 'POST',
      data: postdata,
      datatype: 'json',
      success: function(data) {
        if(data.meta.code === 200)
        {
          success(data);
        }
        else
        {
          error(data);
        }
      },
      error: function(err) { alert(err); },
      beforeSend: function(xhr) {
        xhr.setRequestHeader("X-Mashape-Authorization", "<?php echo $mashape_key; ?>");
      }
    });

    return false;
}

function error(data)
{
  $("form").prepend(
    "<div class='alert alert-danger fade in'>"+
      "<strong>Error!</strong> " + data.data.message +
      "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
    "</div>");
}

function success(data)
{
  $("form").prepend(
    "<div class='alert alert-success alert-dismissable fade in'>"+
      "<strong>Created!</strong> Dataset <a class='alert-link' href='edit.php?dataset=" + data.data.name + "'>" + data.data.name + "</a> successfully created. "+
      "Now try <a class='alert-link' href='upload.php?dataset="+data.data.name+"'>uploading</a> some data."+
      "<button type='button' class='close pull-right' data-dismiss='alert' aria-hidden='true'>&times;</button>"+
    "</div>");
}

</script>
  </body>
</html>

