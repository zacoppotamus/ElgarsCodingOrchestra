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

    /*!
     * Send a simple /ping request to the server to ensure that
     * the API is online and responding.
     */

    public static function ping() {
        $url = self::generate_endpoint_url("ping");
        $data = self::send_request($url, "GET", null, 5);

        if($data) {
            $json = json_decode($data, true);

            if(is_array($json)) {
                return ($json['meta']['code'] == 200);
            }
        }

        return false;
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