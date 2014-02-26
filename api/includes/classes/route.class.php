<?php

/*!
 * This class takes an input nice looking URI, strips it down
 * and tries to match it with the rules that it's been given. The
 * advantage of doing this is that we can specify nice looking URIs
 * in PHP while bypassing Apache or Nginx's rewrite limitations.
 */

class Route {
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";

    // Store an array of our defined routes.
    private static $routes = array();

    /*!
     * Add a new route to the defined routes array, provided both
     * a request method and the regex for the path.
     */

    public static function add($method, $path, $callback) {
        self::$routes[$method][$path] = $callback;
    }

    /*!
     * Parse a request URI to see if we have any matches, and then
     * execute the related callback function with the provided params.
     */

    public static function parse() {
        $url = isset($_GET['uri']) ? trim($_GET['uri']) : null;
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper(trim($_SERVER['REQUEST_METHOD'])) : "GET";

        if(isset(self::$routes[$method])) {
            foreach(self::$routes[$method] as $path => $callback) {
                if(preg_match($path, $url, $params)) {
                    array_shift($params);

                    return call_user_func_array($callback, array_values($params));
                }
            }
        }

        return false;
    }
}

?>