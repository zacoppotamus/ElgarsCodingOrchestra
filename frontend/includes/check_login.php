<?php

if(empty($mashape_key) || empty($user)) {
    header("Location: /login.php?dest=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

?>