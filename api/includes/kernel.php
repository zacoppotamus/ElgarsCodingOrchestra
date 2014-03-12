<?php

// Include the necessary classes.
include("classes/app.class.php");
include("classes/curl.class.php");
include("classes/redis.class.php");
include("classes/xcache.class.php");

include("classes/rainhawk/rainhawk.class.php");
include("classes/rainhawk/dataset.class.php");
include("classes/rainhawk/sets.class.php");

/*!
 * Set the timezone properly.
 */

date_default_timezone_set("UTC");

/*!
 * Make a new instance of App, which is a global singleton that
 * we can use to get vital information about the application's
 * current state and some global variables. In this block, we
 * define all the required variables depending on what machine
 * we're running on.
 */

app::$development = (file_exists("/vagrant") || stripos($_SERVER['HTTP_HOST'], "dev") !== false);
app::$debug = (isset($_GET['d3bug']));
app::$maintenance = false;
app::$version = "β";
app::$init_time = microtime(true);

app::$stack = array(
    "redis" => array(
        "host" => "127.0.0.1",
        "port" => 6379
    ),
    "mongodb" => array(
        "host" => "127.0.0.1",
        "port" => 27017,
        "database" => "eco"
    )
);

if(!app::$development) {
    app::$root_path = "/home/www/spe.sneeza.me/";
} else {
    app::$debug = true;
    app::$root_path = "/vagrant/www/";
}

if(app::$debug) {
    ini_set("display_errors", 1);
    error_reporting(E_ALL & ~E_NOTICE);
}

/*!
 * Create a new instance of the cache, depending on which deployment
 * we're on. We need one instance of xcache, which is our local
 * cache, and one instance of redis, which is our global cache.
 */

xcache::init();
redis::connect(app::$stack['redis']['host'], app::$stack['redis']['port']);

/*!
 * Set the user agent to be used in the cURL singleton.
 */

curl::$timeout = 6;
curl::$user_agent = "ECO " . app::$version . "; spe.sneeza.me;";

/*!
 * Connect to MongoDB so that we can run queries on different data
 * sets that have been imported.
 */

rainhawk::connect(app::$stack['mongodb']['host'], app::$stack['mongodb']['port']);
rainhawk::select_database(app::$stack['mongodb']['database']);

/*!
 * Check if the Mashape key has been set, and if not then use a
 * default key so we don't break direct integration with our test
 * services.
 */

app::$username = isset($_SERVER['HTTP_X_MASHAPE_USER']) ? trim(strtolower($_SERVER['HTTP_X_MASHAPE_USER'])) : "global";

?>