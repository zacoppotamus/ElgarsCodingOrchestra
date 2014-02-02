<?php


function isIndex($fieldName, $words)
{
    foreach($words as $word)
    {
        if(strpos(strtolower($fieldName), strtolower($word)) !== false)
        {
            echo "true";
            return true;
        }
    }
    echo "false";
    return false;

}

$keywords = array("name", "id", "key");
isIndex("thedis", $keywords);

?>
