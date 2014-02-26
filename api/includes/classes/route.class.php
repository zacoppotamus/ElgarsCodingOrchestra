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

    /*!
     * Add a new route to the defined routes array.
     */

    public static function add($path, $callback) {
        self::$routes[$path] = $callback;
    }

    /*!
     * Parse a request URI to see if we have any matches.
     */

    public static function parse() {
        foreach(self::$routes as $path => $callback) {
            if(preg_match($pattern, $url, $params)) {
                array_shift($params);

                return call_user_func_array($callback, array_values($params));
            }
        }

        return false;
    }
}

?>