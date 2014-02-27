<?php

$mashape_key = $_POST["apiKey"];
$url = "https://sneeza-eco.p.mashape.com/datasets";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "ECO / Login Wrapper 0.1");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $this->mashape_key));
$result = curl_exec($ch);
curl_close($ch);

?>
<html>
    <head>
        <title>Account Page</title>
    </head>
    <body>
        <p>Your API key is <?php echo $mashape_key; ?></p>
        <p><?php echo $result ?></p>
    </body>
</html>
