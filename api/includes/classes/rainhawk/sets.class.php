<?php

namespace Rainhawk;

/**
 * Rainhawk\Sets
 *
 * Dataset class to be used in the Rainhawk framework. This
 * class provides an interface for feeding data into and out
 * from a collection in MongoDB.
 *
 * @package Rainhawk\Sets
 */

class Sets {
    /**
     * Holds a reference to our "system.datasets" collection which
     * has information about each dataset.
     *
     * @var MongoCollection
     */

    private static $datasets;

    /**
     * Takes an input dataset ID and returns whether or not that
     * dataset was found in the reference table or not.
     *
     * @param string $prefix  The prefix for the user.
     * @param string $name  The name of the dataset.
     * @return bool  Whether the dataset was found or not.
     */

    public static function exists($prefix, $name) {
        $datasets = self::get_system_datasets();

        try {
            $results = $datasets->find(array("prefix" => $prefix, "name" => $name));

            return ($results->count() > 0);
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Fetch the metadata about a dataset from the "system.datasets"
     * collection, which stores everything about each set in the database.
     *
     * @param string $prefix  The prefix for the user.
     * @param string $name  The name of the dataset.
     * @return array  An array of information about the set.
     */

    public static function fetch_metadata($prefix, $name) {
        $datasets = self::get_system_datasets();

        try {
            $results = $datasets->find(array("prefix" => $prefix, "name" => $name));
            $data = $results->getNext();

            unset($data['_id']);

            return $data;
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Fetch a list of all of the datasets that the specified API
     * key has read or write access to.
     *
     * @param string $api_key  The API key to search for.
     * @return MongoCursor  A cursor to the list of datasets.
     */

    public static function sets_for_api_key($api_key) {
        $datasets = self::get_system_datasets();

        try {
            $results = $datasets->find(array('$or' => array(array("read_access" => $api_key), array("write_access" => $api_key))));

            return $results;
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Check if the $datasets variable has been set, and if not
     * then set it.
     *
     * @return MongoCollection  The collection containing the information.
     */

    private static function get_system_datasets() {
        if(self::$datasets) {
            return self::$datasets;
        }

        self::$datasets = rainhawk::fetch_collection("system.datasets");
        return self::$datasets;
    }
}

?>