<?php

/**
 * Rainhawk
 *
 * The main class for our Rainhawk framework, providing an
 * interface from the application to the MongoDB database layer
 * so that we can grab data.
 *
 * @package Rainhawk
 */

class Rainhawk {
    /**
     * Store a connection instance for our database layer.
     *
     * @var MongoClient
     */

    private static $connection;

    /**
     * Store a reference for our MongoDatabase instance.
     *
     * @var MongoDatabase
     */

    private static $database;

    /**
     * Connect to the specified MongoDB server, which defaults to localhost on
     * Mongo's default port of 27017 (coincidentally also Valve's Source server
     * IP range but w/e).
     *
     * @param string $host  The hostname or IP of the Mongo server.
     * @param integer $port  The port that the server is running on.
     * @return bool  Whether the connection was a success or not.
     */

    public static function connect($host = "127.0.0.1", $port = 27017) {
        return (self::$connection = new MongoClient("mongodb://" . $host . ":" . $port . "/"));
    }

    /**
     * Grab a reference to a MongoDatabase object so that we can grab collection
     * objects to pass through to child classes.
     *
     * @param string $database  The name of the database.
     * @return bool  Whether the database was selected or not.
     */

    public static function select_database($database) {
        return (self::$database = self::$connection->selectDB($database));
    }

    /**
     * Grab a reference to a MongoCollection object provided a name.
     *
     * @param string $name  The name of the collection.
     * @return MongoCollection  The object.
     */

    public static function select_collection($name) {
        return self::$database->selectCollection($name);
    }
}

?>