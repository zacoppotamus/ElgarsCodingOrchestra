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
    private static $datasets = null;

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
        try {
            self::$database = self::$conn->selectDB($database);
        } catch(Exception $e) {
            return false;
        }

        return true;
    }

    /*!
     * Select the collection to be used for storing/retreiving the data.
     * If the collection doesn't exist, Mongo will create one for us.
     */

    public static function select_collection($collection) {
        try {
            return self::$database->selectCollection($collection);
        } catch(Exception $e) {
            return false;
        }
    }

    /*!
     * Check if a collection exists so that we can prevent people creating
     * two sets with the same name.
     */

    public static function collection_exists($name) {
        $datasets = self::datasets();
        $matches = $datasets->find(array("name" => $name));

        return ($matches->count() > 0);
    }

    /*!
     * Check that a user has access to a specific collection by validating
     * their ownership with the datasets table.
     */

    public static function can_access_collection($name) {
        $datasets = self::datasets();
        $matches = $datasets->find(array("name" => $name, "accessors" => app::$mashape_key));

        return ($matches->count() > 0);
    }

    /*!
     * Get the statistics for a collection given it's label, which is
     * provided by the user.
     */

    public static function get_collection_info($label) {
        //
    }

    /*!
     * Get a list of all of the available collections in the database.
     * This is useful for a variety of reasons.
     */

    public static function get_collections_for_key($mashape_key) {
        $datasets = self::select_collection("system.datasets");

        return $datasets->find(array("owners" => $mashape_key));
    }

    /*!
     * Create a new collection in the database using the specified name
     * and optional parameters.
     */

    public static function create_collection($name, $mashape_key) {
        $internal_name = $mashape_key . "." . $name;
        $collection = self::$database->createCollection($internal_name);

        if($collection) {
            $datasets = self::select_collection("system.datasets");
            $datasets->insert(array(
                "name" => "tes034923",
                "label" => $name,
                "created" => time(),
                "rows" => 0,
                "fields" => array(),
                "have_access" => array($mashape_key)
            ));
        }

        return false;
    }

    /*!
     * Private function for getting a handler to the datasets collection
     * so that we can check which tables exist.
     */

    private static function datasets() {
        if(isset(self::$datasets)) {
            return self::$datasets;
        }

        return self::select_collection("system.datasets");
    }

    /*!
     * A public function to generate a safe name for a dataset, so that users
     * can't access system sets or other user's sets.
     */

    public static function safe_name($name) {
        return "data." . $name;
    }
}
