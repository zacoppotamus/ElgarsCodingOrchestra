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
    private $id;
    private $name;
    private $description;
    private $rows = 0;
    private $fields = array();
    private $read_access = array();
    private $write_access = array();
    private $exists = false;

    /**
     * Create a new instance of Dataset, which initiates itself
     * using the provided ID and fetches relevant information from
     * MongoDB and our internal dataset structure.
     *
     * @param string $id  The ID of the dataset to fetch.
     * @return Dataset  Our new Dataset instance.
     */

    public function __construct($id) {
        $this->id = $id;

        if(rainhawk\sets::exists($id)) {
            $set_data = rainhawk\sets::fetch_metadata($id);
            $this->collection = rainhawk::fetch_collection($id);

            $this->name = $set_data['name'];
            $this->description = $set_data['description'];
            $this->rows = (int)$set_data['rows'];
            $this->fields = $set_data['fields'];
            $this->read_access = $set_data['read_access'];
            $this->write_access = $set_data['write_access'];
            $this->exists = true;
        }
    }

    public function have_read_access($api_key) {
        return $this->exists && in_array($api_key, $this->read_access);
    }

    public function have_write_access($api_key) {
        return $this->exists && in_array($api_key, $this->write_access);
    }

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