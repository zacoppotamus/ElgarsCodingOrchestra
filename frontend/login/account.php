<?php
$mashape_key = $_POST["apiKey"];

$ch = curl_init();

$url = "https://sneeza-eco.p.mashape.com/datasets";

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "ECO / Login System 0.1");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $mashape_key));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$result = curl_exec($ch);
if ($result == "{\"message\":\"Invalid Mashape key. If you are a Mashape user, get your key from your dashboard at https://www.mashape.com/login - To create a free Mashape account instead, go to https://www.mashape.com/signup\"}")
{
    $result = "Failed login";
}

curl_close($ch);
?>

<html>
    <head>
        <title>Account Page</title>
    </head>
    <body>
        <div id="Result"></div>
        <p>Your API key is <?php echo $mashape_key; ?></p>
        <p>Your result is <?php echo $result; ?></p>
    </body>
</html>
