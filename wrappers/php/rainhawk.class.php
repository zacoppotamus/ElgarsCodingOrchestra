<?php

/**
 * Project Rainhawk
 *
 * Simple PHP wrapper for the Rainhawk API, which provides simple JSON
 * encoded data for a variety of data sources with pre-defined
 * operations.
 *
 * @package Rainhawk
 * @license none
 */

class Rainhawk {
    /**
     * Store some constants for the different request methods.
     *
     * @var string
     */

    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";
    const PUT_FILE = "PUTFILE";

    /**
     * Store the base address for the API.
     *
     * @var string
     */

    private $host = "https://sneeza-eco.p.mashape.com/";

    /**
     * Store the Mashape API key to use for authentication to the
     * API. Without this, no requests will succeed.
     *
     * @var string
     */

    private $mashapeKey = null;

    /**
     * Store the last known error number, which is fetched from the
     * API meta response.
     *
     * @var integer
     */

    private $errno = null;

    /**
     * Store the last known error message, which is also fetched from
     * the API data response.
     *
     * @var string
     */

    private $error = null;

    /**
     * When the class is initialised, we have to store the Mashape key
     * in the class for all requests.
     *
     * @param string $mashapeKey  The mashape key to use for all requests.
     * @return eco  Our new class instance.
     */

    public function __construct($mashapeKey) {
        $this->mashapeKey = $mashapeKey;

        if(substr($this->host, -1) == "/") {
            $this->host = substr($this->host, 0, -1);
        }
    }

    /**
     * Send a simple ping request to the API, which will respond with
     * the timestamp of the server's current time.
     *
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function ping() {
        $url = $this->host . "/ping";
        $data = $this->sendRequest($url, self::GET);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Fetch a list of available datasets, in no particular order, returned
     * as an array from the API. We only get back the datasets that we have
     * read or write access to.
     *
     * @return array|bool  Returns the datasets on success, false on failure.
     */

    public function datasets() {
        $url = $this->host . "/datasets";
        $data = $this->sendRequest($url, self::GET);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data']['datasets'];
    }

    /**
     * Get the dataset information, including the total rows and field
     * names. We can also use this to check whether or not the dataset
     * exists.
     *
     * @param string $name  The dataset name.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function fetchDataset($name) {
        $url = $this->host . "/datasets/" . $name;
        $data = $this->sendRequest($url, self::GET);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Create a new dataset, with a specified name and description. These
     * are both mandatory.
     *
     * @param string $name  The dataset's name.
     * @param string $description  The dataset's description.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function createDataset($name, $description) {
        $postData = array(
            "name" => $name,
            "description" => $description
        );

        $url = $this->host . "/datasets";
        $data = $this->sendRequest($url, self::POST, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Delete the specified dataset, provided you have write access to the
     * set. This operation cannot be reversed.
     *
     * @param string $name  The dataset's name.
     * @return bool  Returns the boolean on success, false on failure.
     */

    public function deleteDataset($name) {
        $url = $this->host . "/datasets/" . $name;
        $data = $this->sendRequest($url, self::DELETE);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data']['deleted'];
    }

    /**
     * Run a select query, finding results from a dataset that match
     * certain conditions. Optionally, leave the query blank to return
     * all rows.
     *
     * @param string $name  The dataset name to query.
     * @param array $query  The query to run, in MongoDB format.
     * @param int $offset  Apply an offset on the query, for pagination.
     * @param int $limit  Apply a row limit on the query.
     * @param array $sort  The sorting order, in array format.
     * @param array $fields  The fields to return from the query.
     * @param array $exclude  The fields to exclude from the results.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function selectData($name, $query = null, $offset = 0, $limit = 0, $sort = null, $fields = null, $exclude = null) {
        $query_string = array(
            "query" => json_encode($query),
            "offset" => $offset,
            "limit" => $limit,
            "sort" => json_encode($sort),
            "fields" => json_encode($fields),
            "exclude" => json_encode($exclude)
        );

        $url = $this->host . "/datasets/" . $name . "/data";
        $data = $this->sendRequest($url, self::GET, $query_string);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Insert is an alias of insertMulti, and we just manipulate the
     * parameters passed to wrap one row to look like an array of
     * rows.
     *
     * @param string $name  The dataset to insert rows into.
     * @param array $row  The row to insert.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function insertData($name, $row) {
        return $this->insertMultiData($name, array($row))[0];
    }

    /**
     * Insert multiple rows into a dataset. The API will return
     * a success parameter as well as the number of rows added
     * which we can use to verify our request.
     *
     * @param string $name  The dataset to insert the rows into.
     * @param array $rows  The rows to insert.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function insertMultiData($name, $rows) {
        $postData = array(
            "rows" => json_encode($rows)
        );

        $url = $this->host . "/datasets/" . $name . "/data";
        $data = $this->sendRequest($url, self::POST, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data']['rows'];
    }

    /**
     * Update documents using a query, which can be used to match
     * multiple or single documents. The change is essentially just
     * a diff for the record, and the magic keyword \$unset can be
     * sent to remove a field totally.
     *
     * @param string $name  The dataset to update documents in.
     * @param array $query  The query to use to find documents to update.
     * @param array $changes  The changes to make.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function updateData($name, $query, $changes) {
        $postData = array(
            "query" => json_encode($query),
            "changes" => json_encode($changes)
        );

        $url = $this->host . "/datasets/" . $name . "/data";
        $data = $this->sendRequest($url, self::PUT, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Delete documents from the database using a query to match
     * documents in a specified dataset.
     *
     * @param string $name  The dataset that we should delete rows from.
     * @param array $query  The query to match rows to delete.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function deleteData($name, $query) {
        $postData = array(
            "query" => json_encode($query)
        );

        $url = $this->host . "/datasets/" . $name . "/data";
        $data = $this->sendRequest($url, self::DELETE, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Upload a data file to the specified dataset. We currently support lots
     * of different types of files including .ods, .csv, .txt and more.
     *
     * @param string $name  The dataset to insert the data into.
     * @param string $file  The path to the file to upload.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function uploadData($name, $file) {
        $fp = fopen($file, "rb");
        $size = filesize($file);

        $postData = array(
            "fp" => $fp,
            "size" => $size
        );

        $url = $this->host . "/datasets/" . $name . "/upload/" . pathinfo($file, PATHINFO_EXTENSION);
        $data = $this->sendRequest($url, self::PUT_FILE, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * List the indexes that have been created on a dataset. We
     * should have at least one on _id by default.
     *
     * @param string $name  The dataset that we should list indexes on.
     * @return array|bool  Returns the indexes on success, false on failure.
     */

    public function fetchIndexes($name) {
        $url = $this->host . "/datasets/" . $name . "/indexes";
        $data = $this->sendRequest($url, self::GET);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data']['indexes'];
    }

    /**
     * Create an index on the specified dataset, which currently
     * only works on a single field.
     *
     * @param string $name  The dataset to create an index on.
     * @param array $fields  The fields to index. An blank field will cause auto-indexing.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function addIndex($name, $fields = array()) {
        $postData = array(
            "fields" => json_encode($fields)
        );

        $url = $this->host . "/datasets/" . $name . "/indexes";
        $data = $this->sendRequest($url, self::POST, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Get the dataset's access list, which requires read access to view. Here
     * we can see which usernames have been given access to read and write from
     * and to the dataset.
     *
     * @param string $name  The dataset name.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function fetchAccessList($name) {
        $url = $this->host . "/datasets/" . $name . "/access";
        $data = $this->sendRequest($url, self::GET);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Give a user access to the specified dataset, which requires you to
     * send the type and the username in a POST request.
     *
     * @param string $name  The dataset to create an index on.
     * @param string $type  The type of access to give, "read" and "write".
     * @param string $username  The username to give access to.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function giveAccess($name, $type, $username) {
        $postData = array(
            "type" => $type,
            "username" => $username
        );

        $url = $this->host . "/datasets/" . $name . "/access";
        $data = $this->sendRequest($url, self::POST, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Remove a user's access from the specified dataset, which requires
     * you to have write access to the set.
     *
     * @param string $name  The dataset to create an index on.
     * @param string $type  The type of access to give, "read" and "write".
     * @param string $username  The username to give access to.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function removeAccess($name, $type, $username) {
        $postData = array(
            "type" => $type,
            "username" => $username
        );

        $url = $this->host . "/datasets/" . $name . "/access";
        $data = $this->sendRequest($url, self::DELETE, $postData);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Calculate the linear regression of two fields of data inside a
     * dataset, to the n-th degree.
     *
     * @param string $name  The dataset that we run calculations on.
     * @param array $fields  The two fields to use.
     * @param int $degree  The degree of the polynomial.
     * @return array|bool  Returns the coefficients on success, false on failure.
     */

    public function calcPolyfit($name, $fields, $degree = 2) {
        $query_string = array(
            "fields" => json_encode($fields),
            "degree" => $degree
        );

        $url = $this->host . "/datasets/" . $name . "/calc/polyfit";
        $data = $this->sendRequest($url, self::GET, $query_string);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data']['coefficients'];
    }

    /**
     * Calculate some statistics about a set of data, which can be
     * limited to a subset of data in a dataset using a query or the
     * entire table.
     *
     * @param string $name  The dataset that we run calculations on.
     * @param string $field  The field to run the calculations on.
     * @param array $query  The query to run, in MongoDB format.
     * @return array|bool  Returns the stats on success, false on failure.
     */

    public function calcStats($name, $field, $query = array()) {
        $query_string = array(
            "field" => $field,
            "query" => $query
        );

        $url = $this->host . "/datasets/" . $name . "/calc/stats";
        $data = $this->sendRequest($url, self::GET, $query_string);
        $json = $this->parseJson($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Return the last known error code, which can be populated from
     * the API or a failed cURL request.
     *
     * @return integer  The error code.
     */

    public function errno() {
        return $this->errno;
    }

    /**
     * Return the last known error message, which can be populated
     * from the API or a failed cURL request.
     *
     * @return string  The error message.
     */

    public function error() {
        return $this->error;
    }

    /**
     * Private function to parse JSON into an array, also setting
     * the error code and message in the class if necessary.
     *
     * @param string $data  The JSON string.
     * @return array|bool  The decoded JSON data on success, or false on failure.
     */

    private function parseJson($data) {
        $json = json_decode($data, true);

        if(!is_array($json)) {
            $this->errno = 402;
            $this->error = "Invalid JSON received from API.";

            return false;
        }

        if($json['meta']['code'] !== 200) {
            $this->errno = $json['meta']['code'];
            $this->error = $json['data']['message'];

            return false;
        }

        return $json;
    }

    /**
     * Private function to make a cURL request to the API using two
     * different methods to send the data - POST and GET.
     *
     * @param string $url  The web address to fetch.
     * @param string $method  GET, POST, PUT or DELETE.
     * @param string $params  The query parameters or POST parameters.
     * @param integer $timeout  The timeout to use for the request.
     * @return string  The response data from the request.
     */

    private function sendRequest($url, $method, $params = null, $timeout = 10) {
        $ch = curl_init();

        if($method == self::GET && !empty($params)) {
            $url .= "?" . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Rainhawk / PHP Wrapper 1.0");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $this->mashapeKey));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if($method == self::POST) {
            curl_setopt($ch, CURLOPT_POST, true);

            if(!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        } else if($method == self::PUT || $method == self::DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if(!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        } else if($method == self::PUT_FILE) {
            curl_setopt($ch, CURLOPT_PUT, true);

            curl_setopt($ch, CURLOPT_INFILE, $params['fp']);
            curl_setopt($ch, CURLOPT_INFILESIZE, $params['size']);
        }

        $result = curl_exec($ch);

        if(!$result) {
            $this->errno = curl_errno($ch);
            $this->error = curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }
}

?>