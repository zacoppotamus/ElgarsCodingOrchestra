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
     * Given an array of field names, return which ones we think should
     * be indexed and which are non-essential.
     */

    public static function find_index_names($fields) {
        $keywords = array("id", "name", "key");
        $names = array();

        foreach($fields as $field_name) {
            $matched = false;

            foreach($keywords as $keyword) {
                if(stripos($field_name, $keyword) !== false) {
                    $matched = true;
                    break;
                }
            }

            if($matched) {
                $names[] = $field_name;
            }
        }

        return $names;
    }

    /*!
     * Generate the humanly readable runtime, in seconds.
     */

    public static function runtime() {
        return round(microtime(true) - self::$init_time, 3);
    }

    /*!
     * Log something, somewhere.
     */

    public static function log($section, $message) {
        return error_log("[$section] $message\n", 0);
    }
}

?>