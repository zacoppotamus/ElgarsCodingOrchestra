<?php

/*!
 * This class acts as a wrapper between our application and
 * the XCache var cache, allowing us to store variables in
 * RAM.
 */

class XCache {
    // Define some public variables for whether we should use
    // this class or not.
    public static $enabled = false;

    // Store some stats about the class.
    public static $access_time = 0;
    public static $hits = 0;
    public static $misses = 0;

    /*!
     * Initiate the cache so that we can use it elsewhere in
     * our application, otherwise every other call is bypassed by
     * a flag.
     */

    public static function init() {
        self::$enabled = true;
    }

    /*!
     * Fetch a keyvalue from the variable cache, so that we can
     * mess around with some data!
     */

    public static function fetch($key) {
        if(!self::$enabled) {
            return null;
        }

        $start = microtime(true);
        $data = xcache_get($key);
        $data = ($data) ? unserialize($data) : null;

        if(!is_null($data)) {
            self::$hits++;
        } else {
            self::$misses++;
        }

        $finish = microtime(true);

        self::$access_time += $finish - $start;

        return $data;
    }

    /*!
     * Store some data related to a key for a specified (or unspecified
     * amount of time).
     */

    public static function store($key, $data, $ttl = 0) {
        if(!self::$enabled) {
            return false;
        }

        $data = serialize($data);

        return xcache_set($key, $data, $ttl);
    }

    /*!
     * Remove a key from the local variable cache, which is generally
     * used when we have data we want to update.
     */

    public static function remove($key) {
        if(!self::$enabled) {
            return false;
        }

        return xcache_unset($key);
    }
}

?>