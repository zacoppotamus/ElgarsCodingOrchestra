<?php

/**
 * Project Rainhawk
 *
 * Simple PHP wrapper for the ECO API, which provides simple JSON
 * encoded data for a variety of data sources with pre-defined
 * operations.
 *
 * @package eco
 * @license none
 */

class eco {
    /**
     * Store the address of the base address for the API.
     *
     * @var string
     */

    private $host = "https://sneeza-eco.p.mashape.com/";

    /**
     * Store the Mashape API key to use for authenticatino to the
     * API. Without this, no requests will succeed.
     *
     * @var string
     */

    private $mashape_key = null;

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
     * @param string $mashape_key  The mashape key to use for all requests.
     * @return eco  Our new class instance.
     */

    public function __construct($mashape_key) {
        $this->mashape_key = $mashape_key;

        if(substr($this->host, -1) == "/") {
            $this->host = substr($this->host, 0, -1);
        }
    }

    /**
     * Send a simple ping request to the API, which will respond with
     * the timestamp of the server's current time.
     *
     * @return int  The server timestamp.
     */

    public function ping() {
        $url = $this->host . "/ping";
        $data = $this->send_request($url, "GET");
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data']['server_time'];
    }

    /**
     * Fetch a list of available datasets, in no particular order, returned
     * as an array from the API.
     *
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function datasets() {
        $url = $this->host . "/datasets";
        $data = $this->send_request($url, "GET");
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data']['datasets'];
    }

    /**
     * Run a /select query, finding results from a dataset that match
     * certain conditions. Optionally, leave the query blank to return
     * all rows.
     *
     * @param string $dataset  The dataset name to query.
     * @param array $query  The query to run, in MongoDB format.
     * @param int $limit  Apply a row limit on the query.
     * @param int $offset  Apply an offset on the query, for pagination.
     * @param array $fields  The fields to return from the query.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function select($dataset, $query = null, $offset = 0, $limit = 0, $fields = null) {
        $query_string = array(
            "query" => json_encode($query),
            "offset" => $offset,
            "limit" => $limit,
            "fields" => json_encode($fields)
        );

        $url = $this->host . "/datasets/" . $dataset . "/select";
        $data = $this->send_request($url, "GET", $query_string);
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Insert is an alias of insert_multi, and we just manipulate the
     * parameters passed to wrap one document to look like an array of
     * documents.
     *
     * @param string $dataset  The dataset to insert documents into.
     * @param array $document  The document to insert.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function insert($dataset, $document) {
        return $this->insert_multi($dataset, array($document));
    }

    /**
     * Insert multiple documents into a dataset. The API will return
     * a success parameter as well as the number of documents added
     * which we can use to verify our request.
     *
     * @param string $dataset  The dataset to insert documents into.
     * @param array $document  The documents to insert.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function insert_multi($dataset, $documents) {
        $post_data = array(
            "documents" => json_encode($documents)
        );

        $url = $this->host . "/datasets/" . $dataset . "/insert";
        $data = $this->send_request($url, "POST", $post_data);
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Update documents using a query, which can be used to match
     * multiple or single documents. The change is essentially just
     * a diff for the record, and the magic keyword \$unset can be
     * sent to remove a field totally.
     *
     * @param string $dataset  The dataset to update documents in.
     * @param array $query  The query to use to find documents to update.
     * @param array $changes  The changes to make.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function update($dataset, $query, $changes) {
        $post_data = array(
            "query" => json_encode($query),
            "changes" => json_encode($changes)
        );

        $url = $this->host . "/datasets/" . $dataset . "/update";
        $data = $this->send_request($url, "POST", $post_data);
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Delete documents from the database using a query to match
     * documents in a specified dataset.
     *
     * @param string $dataset  The dataset that we should delete rows from.
     * @param array $query  The query to match rows to delete.
     * @return array|bool  Returns the data array on success, false on failure.
     */

    public function delete($dataset, $query) {
        $post_data = array(
            "query" => json_encode($query)
        );

        $url = $this->host . "/datasets/" . $dataset . "/delete";
        $data = $this->send_request($url, "POST", $post_data);
        $json = $this->parse_json($data);

        if(!$json) {
            return false;
        }

        return $json['data'];
    }

    /**
     * Calculate the linear regression of two fields of data inside a
     * dataset, to the n-th degree.
     *
     * @param string $dataset  The dataset that we run calculations on.
     * @param string $field_one  The first field.
     * @param string $field_two  The second field.
     * @param int $degree  The degree of the polynomial.
     * @return array|bool  Returns the coefficients on success, false on failure.
     */

    public function calc_polyfit($dataset, $field_one, $field_two, $degree = 2) {
        $query_string = array(
            "field_one" => $field_one,
            "field_two" => $field_two,
            "degree" => $degree
        );

        $url = $this->host . "/datasets/" . $dataset . "/calc/polyfit";
        $data = $this->send_request($url, "GET", $query_string);
        $json = $this->parse_json($data);

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
     * @param string $dataset  The dataset that we run calculations on.
     * @param string $field_name  The field to run the calculations on.
     * @param array $query  The query to run, in MongoDB format.
     * @return array|bool  Returns the stats on success, false on failure.
     */

    public function calc_stats($dataset, $field_name, $query = array()) {
        $query_string = array(
            "field_name" => $field_name,
            "query" => $query
        );

        $url = $this->host . "/datasets/" . $dataset . "/calc/stats";
        $data = $this->send_request($url, "GET", $query_string);
        $json = $this->parse_json($data);

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

    private function parse_json($data) {
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
     * @param string $method  GET or POST
     * @param string $params  The query parameters or POST parameters.
     * @param integer $timeout  The timeout to use for the request.
     * @return string  The response data from the request.
     */

    private function send_request($url, $method = "GET", $params = null, $timeout = 10) {
        $ch = curl_init();

        if($method == "GET" && !empty($params)) {
            $url .= "?" . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "ECO / PHP Wrapper 1.0");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Mashape-Authorization: " . $this->mashape_key));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);

            if(!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
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