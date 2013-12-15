<?php

/*!
 * Elgar's Coding Orchestra
 *
 * Simple PHP wrapper for the ECO API, which provides simple JSON
 * encoded data for a variety of data sources with pre-defined
 * operations.
 *
 * @created: 2013-12-15
 * @license: none
 */

class eco {
    // Store a reference to the API url and available endpoints.
    private static $host = "http://api.spe.sneeza.me/";
    private static $endpoints = array("search", "insert", "update", "delete", "ping", "calc/polyfit", "calc/mean", "calc/stddev");

    // Store last known errors.
    private static $errno = null;
    private static $error = null;

    /*!
     * Send a simple /ping request to the server to ensure that
     * the API is online and responding.
     */

    public static function ping() {
        $url = self::generate_endpoint_url("ping");
        $data = self::send_request($url, "GET", null, 5);

        if(!$data) {
            return false;
        }

        $json = self::parse_json($data);

        if(!$json) {
            return false;
        }

        return true;
    }

    /*!
     * Insert is an alias of insert_multi, and we just manipulate
     * the parameters passed to wrap one document to look like an
     * array of documents.
     */

    public static function insert($dataset, $document) {
        return self::insert_multi($dataset, array($document));
    }

    /*!
     * Insert multiple documents into a dataset. The API will return
     * a success parameter as well as the number of documents added
     * which we can use to verify our request.
     */

    public static function insert_multi($dataset, $documents) {
        $post_data = array(
            "dataset" => $dataset,
            "documents" => json_encode($documents)
        );

        $url = self::generate_endpoint_url("insert");
        $data = self::send_request($url, "POST", $post_data);

        if(!$data) {
            return false;
        }

        $json = self::parse_json($data);

        if(!$json) {
            return false;
        }

        if($json['data']['inserted'] == count($documents)) {
            return true;
        }

        return false;
    }

    /*!
     * Return the last known error code, which can be populated from
     * the API or a failed cURL request.
     */

    public static function errno() {
        return self::$errno;
    }

    /*!
     * Return the last known error message, which can be populated
     * from the API or a failed cURL request.
     */

    public static function error() {
        return self::$error;
    }

    /*!
     * Private function to parse JSON into an array, also setting
     * the error code and message in the class if necessary.
     */

    private static function parse_json($data) {
        $json = json_decode($data, true);

        if(!is_array($json)) {
            self::$errno = 402;
            self::$error = "Invalid JSON received from API.";

            return false;
        }

        if($json['meta']['code'] !== 200) {
            self::$errno = $json['meta']['code'];
            self::$error = $json['data']['message'];

            return false;
        }

        return $json;
    }

    /*!
     * Private function to generate an API URL given an endpoint
     * name. These are trivial to generate.
     */

    private static function generate_endpoint_url($endpoint) {
        if(!in_array($endpoint, self::$endpoints)) {
            return false;
        }

        return self::$host . $endpoint;
    }

    /*!
     * Private function to make a cURL request to the API using two
     * different methods to send the data - POST and GET.
     */

    private static function send_request($url, $method = "GET", $params = null, $timeout = 20) {
        $ch = curl_init();

        if($method == "GET" && !empty($params)) {
            $url .= "?" . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "ECO PHP Wrapper 1.0");
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);

            if(!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }

        $result = curl_exec($ch);

        if(!$result) {
            self::$errno = curl_errno($ch);
            self::$error = curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }
}