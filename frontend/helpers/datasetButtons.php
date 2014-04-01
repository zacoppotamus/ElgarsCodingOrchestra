<?php

function navButtons($dataset, $isWrite)
{
  $disabled = $isWrite ? "" : "disabled";
  return <<<EOD
    <a href="account.php" type="button" class="btn btn-warning pull-right">
      <i class="fa fa-bars"></i>&nbsp; Datasets
    </a>
    <a href='upload.php?dataset=$dataset' class='btn btn-primary pull-right' $disabled>
      <i class='fa fa-cloud-upload'></i>&nbsp; Upload
    </a>
    <a href='newlogic/?dataset=$dataset' class='btn btn-success pull-right'>
      <i class='fa fa-bar-chart-o'></i>&nbsp; Visualise
    </a>
    <a href='edit.php?dataset=$dataset' class='btn btn-info pull-right'>
      <i class='fa fa-edit'></i>&nbsp; View
    </a>
EOD;
}


?>


