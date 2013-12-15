<?php

/*!
 * This class is a very, very simple OO style wrapper for the
 * cURL extension for PHP. It also offers a quick-access
 * function to fetch a URL in one-line.
 */

class cURL {
    // Define a global variable that can be overridden.
    public static $timeout = 6;
    public static $user_agent = null;

    /*!
     * Pass all of the functions on with an OO style to the
     * base extension below.
     */

    public static function init() {
        return curl_init();
    }

    public static function setopt($ch, $type, $value) {
        return curl_setopt($ch, $type, $value);
    }

    public static function getinfo($ch, $type) {
        return curl_getinfo($ch, $type);
    }

    public static function exec($ch) {
        return curl_exec($ch);
    }

    public static function errno($ch) {
        return curl_errno($ch);
    }

    public static function error($ch) {
        return curl_error($ch);
    }

    public static function close($ch) {
        return curl_close($ch);
    }

    /*!
     * Quick access function to fetch a URL with an optional
     * timeout, which overrides the class 'timeout' variable.
     * We also set a user agent with the version string provided
     * by the app.
     */

    public static function get($url, $timeout = null, $headers = array()) {
        $ch = self::init();

        self::setopt($ch, CURLOPT_URL, $url);
        self::setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        self::setopt($ch, CURLOPT_TIMEOUT, ($timeout) ? $timeout : self::$timeout);
        self::setopt($ch, CURLOPT_HTTPHEADER, $headers);
        self::setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        self::setopt($ch, CURLOPT_RETURNTRANSFER, true);
        self::setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // self::setopt($ch, CURLOPT_ENCODING, "gzip");

        $result = self::exec($ch);
        self::close($ch);

        return $result;
    }

    /*!
     * Quick access function to fetch a URL with an optional
     * timeout, which overrides the class 'timeout' variable.
     * We also set a user agent with the version string provided
     * by the app.
     */

    public static function post($url, $post_data = array(), $timeout = null, $headers = array()) {
        $ch = self::init();

        self::setopt($ch, CURLOPT_URL, $url);
        self::setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        self::setopt($ch, CURLOPT_TIMEOUT, ($timeout) ? $timeout : self::$timeout);
        self::setopt($ch, CURLOPT_HTTPHEADER, $headers);
        self::setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        self::setopt($ch, CURLOPT_RETURNTRANSFER, true);
        self::setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        self::setopt($ch, CURLOPT_POST, true);
        self::setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // self::setopt($ch, CURLOPT_ENCODING, "gzip");

        $result = self::exec($ch);
        self::close($ch);

        return $result;
    }

    /*!
     * Quick access function to perform a HEAD request on a URL
     * and send us back the last_modified header as a timestamp.
     */

    public static function get_last_modified($url, $timestamp, $timeout = null) {
        $ch = self::init();

        self::setopt($ch, CURLOPT_URL, $url);
        self::setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        self::setopt($ch, CURLOPT_TIMEOUT, ($timeout) ? $timeout : self::$timeout);
        self::setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        self::setopt($ch, CURLOPT_RETURNTRANSFER, true);
        self::setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        self::setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
        self::setopt($ch, CURLOPT_TIMEVALUE, $timestamp);
        self::setopt($ch, CURLOPT_FILETIME, true);
        // self::setopt($ch, CURLOPT_ENCODING, "gzip");

        $result = self::exec($ch);
        $code = self::getinfo($ch, CURLINFO_HTTP_CODE);
        $last_modified = self::getinfo($ch, CURLINFO_FILETIME);

        if($code == 200) {
            $result = array(
                "last_modified" => $last_modified,
                "result" => $result
            );
        } else {
            $result = false;
        }

        self::close($ch);

        return $result;
    }
}

?>