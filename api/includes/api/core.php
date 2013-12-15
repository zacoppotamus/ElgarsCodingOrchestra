<?php

/*!
 * Define different generic error messages, pre-json_encoded
 * for ease of use.
 */

define("JSON_ERROR_MAINTENANCE", json_encode(array(
    "meta" => array(
        "code" => 503
    ),
    "data" => array(
        "message" => "Maintenance is currently being performed."
    )
)));

define("JSON_ERROR_NOMETHOD", json_encode(array(
    "meta" => array(
        "code" => 402
    ),
    "data" => array(
        "message" => "No method was specified."
    )
)));

/*!
 * Define a function to handle displaying responses in a
 * simple to use way, with error messages and normal responses.
 */

function json_render($code = 200, $data = array()) {
    return json_encode(array(
        "meta" => array(
            "code" => $code
        ),
        "data" => $data
    ));
}

function json_render_error($code = 300, $message) {
    return json_render($code, array(
        "message" => $message
    ));
}

/*!
 * Define a function that takes JSON input and formats it nicely
 * into good looking JSON that's humanly readable.
 */

function json_beautify($json) {
    if(phpversion() && phpversion() >= 5.4) {
        return json_encode(json_decode($json, true), JSON_PRETTY_PRINT);
    }

    $result = "";
    $pos = 0;
    $length = strlen($json);
    $indent = "    ";
    $newline = "\n";
    $previous_character = "";
    $out_of_quotes = true;

    for($i = 0; $i <= $length; $i++) {
        $char = substr($json, $i, 1);

        if($char == '"' && $previous_character != '\\') {
            $out_of_quotes = !$out_of_quotes;
        } else if(($char == "}" || $char == "]") && $out_of_quotes) {
            $result .= $newline;
            $pos--;

            for($j = 0; $j < $pos; $j++) {
                $result .= $indent;
            }
        }

        if($char == ":" && $previous_character == '"') $char .= " ";

        $result .= $char;

        if(($char == "," || $char == "{" || $char == "[") && $out_of_quotes) {
            $result .= $newline;
            if($char == "{" || $char == "[") $pos++;

            for($j = 0; $j < $pos; $j++) {
                $result .= $indent;
            }
        }

        $previous_character = $char;
    }

    return $result;
}

/*!
 * If our App is currently in maintenance mode, we instantly
 * kill all current operations, and output the maintenance
 * page. This page does not contain any includes so we can be
 * sure that it will not cause strain or perform any queries
 * on the server.
 */

if(app::$maintenance) {
    echo json_beautify(JSON_ERROR_MAINTENANCE);
    exit;
}

?>