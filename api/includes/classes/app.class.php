<?php

/*!
 * This class is a global singleton which is used in every script,
 * providing global variables and dependency injection for lots of
 * helper classes.
 */

class App {
    // Some global toggles used when the server is under load.
    public static $development = false;
    public static $maintenance = false;
    public static $debug = false;

    // App specific variables.
    public static $version = null;
    public static $domain = null;

    // Server configuration variables.
    public static $stack = array();
    public static $root_path = null;
    public static $init_time = null;

    /*!
     * Generate the humanly readable runtime, in seconds.
     */

    public static function runtime() {
        return round(microtime(true) - self::$init_time, 3);
    }
}

?>