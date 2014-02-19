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
     * @param string $id  The dataset ID.
     * @return bool  Whether the dataset was found or not.
     */

    public static function exists($id) {
        $datasets = self::get_system_datasets();
        $results = $datasets->find(array("id" => $id));

        return ($results->count() > 0);
    }

    /**
     * Fetch the metadata about a dataset from the "system.datasets"
     * collection, which stores everything about each set in the database.
     *
     * @param string $id  The dataset to get the information of.
     * @return array  An array of information about the set.
     */

    public static function fetch_metadata($id) {
        $datasets = self::get_system_datasets();
        $results = $datasets->find(array("id" => $id));
        $data = $results->getNext();

        unset($data['_id']);

        return $data;
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