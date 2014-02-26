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
     * @param array $fields  The fields to return.
     * @return MongoCursor  The Mongo cursor.
     */

    public function find($query, $fields = array()) {
        if(!$this->exists) {
            return false;
        }

        try {
            return $this->collection->find($query, $options);
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Perform an insertion of a single row of data into the dataset,
     * which just passes the rows to the batch insertion method.
     *
     * @param array $row  The row of data to insert.
     * @return array  The row of data that was inserted with _ids.
     */

    public function insert($row) {
        $row = $this-insert_multi(array($row));

        if(!$row) {
            return false;
        }

        return $row[0];
    }

    /**
     * Perform an insertion of multiple rows of data into the dataset
     * in batch, to make API calls easier.
     *
     * @param array $rows  The rows of data to insert.
     * @return array  The rows that were inserted with _ids.
     */

    public function insert_multi($rows) {
        if(!$this->exists) {
            return false;
        }

        try {
            $result = $this->collection->batchInsert($rows);

            if($result['ok'] == 1) {
                return $rows;
            }
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Perform an update query on the dataset, taking both the query
     * of things to match and the changes to make to those rows.
     *
     * @param array $query  The rows to match.
     * @param array $changes  The changes to make.
     * @return int  The number of rows that were changed.
     */

    public function update($query, $changes) {
        if(!$this->exists) {
            return false;
        }

        try {
            $result = $this->collection->update($query, $changes, array("multiple" => true));

            if($result['ok'] == 1) {
                return (int)$result['n'];
            }
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Perform a delete query on the dataset, using a MongoDB query
     * to select the rows that we need to delete.
     *
     * @param array $query  The rows to match.
     * @return int  The number of rows that have been removed.
     */

    public function delete($query) {
        if(!$this->exists) {
            return false;
        }

        try {
            $result = $this->collection->remove($query, array("justOne" => false));

            if($result['ok'] == 1) {
                return (int)$result['n'];
            }
        } catch(Exception $e) {}

        return false;
    }

    /**
     * List the indexes on this collection, returning them in array
     * format.
     *
     * @return array  The indexes in an array.
     */

    public function fetch_indexes() {
        if(!$this->exists) {
            return false;
        }

        try {
            return $this->collection->getIndexInfo();
        } catch(Exception $e) {}

        return false;
    }

    /**
     * Create an index on a specified field, in a specified
     * direction. If it already exists then nothing will happen.
     *
     * @param array $fields  The indexes to create.
     * @return bool  Whether or not the indexes were created.
     */

    public function add_index($indexes) {
        return false;
    }
}

?>