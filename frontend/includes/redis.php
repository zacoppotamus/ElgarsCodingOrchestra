<?php

/*!
 * This class allows us to connect to a network cache storage
 * to access globally shared objects. We use this for User,
 * Trade and other classes.
 */

class Redis {
    // Define some public variables for connections.
    public static $host = "127.0.0.1";
    public static $port = 6379;
    public static $enabled = false;

    // Store some stats about the class.
    public static $access_time = 0;
    public static $hits = 0;
    public static $misses = 0;
    public static $hit = array();
    public static $missed = array();

    // Define some private variables.
    private static $timeout = 6;
    private static $persistent = false;
    private static $socket = null;

    /*!
     * Connect to the Redis server using the defined host and port,
     * using fsockopen as a data stream. Optionally, accept a toggle
     * for using persistent connections.
     */

    public static function connect($host = null, $port = null, $persistent = false) {
        if($host) self::$host = $host;
        if($port) self::$port = $port;

        $timeout = self::$timeout;
        $flags = STREAM_CLIENT_CONNECT;

        if($persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        self::$enabled = true;
        self::$persistent = $persistent;
        self::$socket = stream_socket_client(self::$host . ":" . self::$port, $errno, $error, $timeout, $flags);
    }

    /*!
     * Close the socket so that we can free up the resource. If we're
     * using persistent connections, then ignore the request.
     */

    public static function close() {
        if(self::$socket && !self::$persistent) {
            fclose(self::$socket);
        }

        return false;
    }

    /*!
     * Define a function for fetching a variable from the Redis store,
     * which stores statistics about the usage of the class.
     */

    public static function fetch($key) {
        if(!self::$socket || !self::$enabled) {
            return null;
        }

        $start = microtime(true);
        $data = self::send_command(array("GET", $key));

        if(!is_null($data)) {
            self::$hits++;
            self::$hit[] = $key;
            $data = unserialize($data);
        } else {
            self::$misses++;
            self::$missed[] = $key;
        }

        $finish = microtime(true);

        self::$access_time += $finish - $start;

        return $data;
    }

    /*!
     * Define a function for fetching multiple variables from the cache
     * in one request, reducing network overhead for simple things.
     */

    public static function fetch_multi() {
        if(!self::$socket || !self::$enabled) {
            return null;
        }

        $args = func_get_args();

        if(count($args) == 1 && is_array($args[0])) {
            $args = array_values($args[0]);
        }

        $start = microtime(true);
        $data = self::send_command(array_merge(array("MGET"), $args));
        $results = array();
        $i = 0;

        foreach($data as $d) {
            if(!is_null($d)) {
                self::$hits++;
                self::$hit[] = $args[$i];
                $d = unserialize($d);
            } else {
                self::$misses++;
                self::$missed[] = $args[$i];
            }

            $results[$args[$i]] = $d;
            $i++;
        }

        $finish = microtime(true);

        self::$access_time += $finish - $start;

        return $results;
    }

    /*!
     * Define a function for fetching a variable from the Redis store,
     * which stores statistics about the usage of the class.
     */

    public static function store($key, $data, $ttl = 0) {
        if(!self::$socket || !self::$enabled) {
            return false;
        }

        $data = serialize($data);
        $args = ($ttl == 0) ? array("SET", $key, $data) : array("SETEX", $key, $ttl, $data);

        return self::send_command($args);
    }

    /*!
     * Delete a key from the Redis cache, which is pretty useful for
     * invalidating cache records.
     */

    public static function remove($key) {
        if(!self::$enabled) {
            return false;
        }

        return self::send_command(array("DEL", $key));
    }

    /*!
     * Run a single FLUSHALL command on the Redis server to clear
     * the entire cache.
     */

    public static function flushall() {
        if(!self::$enabled) {
            return false;
        }

        return self::send_command(array("FLUSHALL"));
    }

    /*!
     * Send the command to the socket specified at the start of
     * the class.
     */

    private static function send_command($args) {
        $command = "*" . count($args) . "\r\n";

        foreach($args as $argument) {
            $command .= "$" . strlen($argument) . "\r\n";
            $command .= $argument . "\r\n";
        }

        fwrite(self::$socket, $command);

        return self::parse_response();
    }

    /*!
     * Parse the response given to us by Redis, depending on the
     * format specified.
     */

    private static function parse_response() {
        $line = fgets(self::$socket);
        list($type, $result) = array($line[0], substr($line, 1, strlen($line) - 3));

        if($type == "-") {
            return null;
        } else if($type == "$") {
            if($result == "-1") {
                return null;
            } else {
                $read = 0;
                $size = intval($result);
                $result = null;

                if($size > 0) {
                    do {
                        $block_size = ($size - $read) > 1024 ? 1024 : ($size - $read);
                        $line = fread(self::$socket, $block_size);
                        $read += strlen($line);
                        $result .= $line;
                    } while ($read < $size);
                }

                fread(self::$socket, 2);
                return $result;
            }
        } else if($type == "*") {
            $count = (int)$result;
            $result = array();

            for($i = 0; $i < $count; $i++) {
                $result[] = self::parse_response();
            }
        }

        return $result;
    }
}