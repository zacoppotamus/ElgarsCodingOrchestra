<?php

/*!
 * This class allows us to connect to a network cache storage
 * to access globally shared objects. We use this for User,
 * Trade and other classes.
 */

class MongoCLI {
    // Hold the host and port in variables.
    public static $host = "127.0.0.1";
    public static $port = 27017;

    // Private connection variable for Mongo.
    private static $conn = null;

    /*!
     * Connect.
     */

    public static function connect($host = null, $port = null) {
        if($host) self::$host = $host;
        if($port) self::$port = $port;

        self::$conn = new Mongo("mongodb://" . self::$host . ":" . self::$port . "/");

        if(self::$conn) {
            return true;
        }

        return false;
    }

    public static function select_database($db) {
        return false;
    }
}