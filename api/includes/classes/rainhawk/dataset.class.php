<?php

namespace Rainhawk;

/**
 * Rainhawk\Dataset
 *
 * Dataset class to be used in the Rainhawk framework. This
 * class provides an interface for feeding data into and out
 * from a collection in MongoDB.
 *
 * @package Rainhawk\Dataset
 */

class Dataset {
    private $collection;
    public $prefix;
    public $name;
    public $description;
    public $rows = 0;
    public $fields = array();
    public $read_access = array();
    public $write_access = array();
    public $exists = false;

    /**
     * Create a new instance of Dataset, which initiates itself
     * using the provided ID and fetches relevant information from
     * MongoDB and our internal dataset structure.
     *
     * @param string $prefix  The prefix for the user.
     * @param string $name  The name of the dataset.
     * @return Dataset  Our new Dataset instance.
     */

    public function __construct($prefix, $name) {
        $this->prefix = $prefix;
        $this->name = $name;

        if(rainhawk\sets::exists($prefix, $name)) {
            $set_data = rainhawk\sets::fetch_metadata($prefix, $name);
            $this->collection = rainhawk::fetch_collection($prefix, $name);

            $this->name = $set_data['name'];
            $this->description = $set_data['description'];
            $this->rows = (int)$set_data['rows'];
            $this->fields = $set_data['fields'];
            $this->read_access = $set_data['read_access'];
            $this->write_access = $set_data['write_access'];
            $this->exists = true;
        }
    }

    /**
     * Check if the supplied API key has read access to the dataset
     * which will always return true if the user created it.
     *
     * @param string $api_key  The API key to check.
     * @return bool  Whether or not the user can access it.
     */

    public function have_read_access($api_key) {
        return $this->exists && in_array($api_key, $this->read_access);
    }

    /**
     * Check if the supplied API key has write access to the dataset
     * which will always return true if the user created it.
     *
     * @param string $api_key  The API key to check.
     * @return bool  Whether or not the user can access it.
     */

    public function have_write_access($api_key) {
        return $this->exists && in_array($api_key, $this->write_access);
    }

    /**
     * Perform a find query on the dataset, which returns a Mongo
     * cursor to the results. We can then use this to iterate through
     * the results.
     *
     * @param array $query  The query to run.
     * @param array $options  The optional extra parameters.
     * @return MongoCursor  The Mongo cursor.
     */

    public function find($query, $options = array()) {
        if(!$this->exists) {
            return false;
        }

        try {
            return $this->collection->find($query, $options);
        } catch(Exception $e) {}

        return false;
    }
}

?>