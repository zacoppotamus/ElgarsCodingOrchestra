<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$datasetInfo = $rainhawk->fetchDataset($dataset);
$fields = $datasetInfo["fields"];
$constraints = $datasetInfo["constraints"];
$errors = array();

if(isset($_GET["autoapply"]))
{
    $result = $rainhawk->addConstraint($dataset);
}
else
{
    foreach($_POST["constraint"] as $field => $constraint)
    {
        if($constraints[$field] != $constraint)
        {
            if(isset($constraints[$field]))
            {
                $result = $rainhawk->removeConstraint($dataset, $field);
                if(isset($result["message"]))
                {
                    $errors[] = $result["message"];
                }
            }

            if($constraint != "none")
            {
                $result = $rainhawk->addConstraint($dataset, $field, $constraint);
                if(isset($result["message"]))
                {
                    $errors[] = $result["message"];
                }
            }
        }
    }
}

header('Location: properties.php?dataset=' . $dataset);
exit();

?>
