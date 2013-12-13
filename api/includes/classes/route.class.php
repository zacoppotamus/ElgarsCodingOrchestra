<?php

/*!
 * This class takes an input nice looking URI, strips it down
 * and tries to match it with the rules that it's been given. The
 * advantage of doing this is that we can specify nice looking URIs
 * in PHP while bypassing Apache or Nginx's rewrite limitations.
 */

class Route {
    // Store an array of our defined routes.
    private static $routes = array();
    public static $file = null;
    public static $params = array();

    // Store public variables for parsing.
    public static $uri = null;
    public static $query = null;

    /*!
     * Add a new route to the defined routes array.
     */

    public static function add($path, $file) {
        self::$routes[$path] = $file;
    }

    /*!
     * Parse a request URI to see if we have any matches.
     */

    public static function parse() {
        self::$params = $_GET;

        $uri = preg_split("/(\/|,)/", self::$uri);

        foreach(self::$routes as $path => $file) {
            $path = preg_split("/(\/|,)/", $path);
            $params = array();
            $valid = true;

            for($i = 0; $i < count($path); $i++) {
                $path_part = (isset($path[$i])) ? $path[$i] : null;
                $uri_part = (isset($uri[$i])) ? $uri[$i] : null;

                if(substr($path_part, 0, 1) == ":") {
                    $params[substr($path_part, 1)] = $uri_part;
                } else if(!($path_part == $uri_part)) {
                    $valid = false;
                    break;
                }
            }

            if(!(count($path) == count($uri))) {
                $valid = false;
            }

            if($valid) {
                self::$file = $file;
                self::$params = array_merge(self::$params, $params);
                return true;
            }
        }

        return false;
    }
}

?>