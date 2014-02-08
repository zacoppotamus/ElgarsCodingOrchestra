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
    private static $database = null;

    /*!
     * Create a new instance of the native Mongo driver, which we're
     * essentially wrapping with this class. We don't need to worry about
     * usernames or passwords.
     */

    public static function connect($host = null, $port = null) {
        if($host) self::$host = $host;
        if($port) self::$port = $port;

        self::$conn = new MongoClient("mongodb://" . self::$host . ":" . self::$port . "/");

        if(self::$conn) {
            return true;
        }

        return false;
    }

    /*!
     * Select the database to use within the Mongo instance, so we can
     * separate out our application's logic.
     */

    public static function select_database($database) {
        return (self::$database = self::$conn->selectDB($database));
    }

    /*!
     * Select the collection to be used for storing/retreiving the data.
     * If the collection doesn't exist, Mongo will create one for us.
     */

    public static function select_collection($collection) {
        return self::$database->selectCollection($collection);
    }

    /*!
     * Get a list of all of the available collections in the database.
     * This is useful for a variety of reasons.
     */

    public static function get_collections() {
        return self::$database->getCollectionNames();
    }
}