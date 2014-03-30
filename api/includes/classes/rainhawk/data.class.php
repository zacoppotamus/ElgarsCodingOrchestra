<?php

namespace Rainhawk;

/**
 * Rainhawk\Data
 *
 * Data class to be used in the Rainhawk framework. This class provides
 * a way to check a data type and enforce rules.
 *
 * @package Rainhawk\Data
 */

class Data {
    /**
     * The different supported data types.
     */

    const STRING = "string";
    const INTEGER = "integer";
    const FLOAT = "float";
    const TIMESTAMP = "timestamp";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const ARR = "array";

    /**
     * Create a new dataset in the system.datasets index, with the
     * relevant data so that we can start using other operations on
     * it.
     *
     * @param string $type  The data type to enforce.
     * @param string $value  The value to check.
     * @return mixed  The result if success, null if false.
     */

    public static function check($type, $value) {
        switch($type) {
            case self::STRING:
                return $value;
                break;

            case self::INTEGER:
                if(is_numeric($value) && is_int($value + 0)) {
                    return $value + 0;
                }
                break;

            case self::FLOAT:
                if(is_numeric($value)) {
                    return $value + 0;
                }
                break;

            case self::TIMESTAMP:
                if(strtotime($value) !== false) {
                    return strtotime($timestamp);
                }
                break;

            case self::LATITUDE:
                if(preg_match("/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}$/", $value)) {
                    return (float)$value;
                }
                break;

            case self::LONGITUDE:
                if(preg_match("/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,6}$/", $value)) {
                    return (float)$value;
                }
                break;

            case self::ARR:
                if(is_array($value) || is_object($value)) {
                    return (array)$value;
                }
        }

        return null;
    }

    /**
     * Detect the data type given a simple string which can be used to
     * automatically work out what constraints to apply to a field or not.
     *
     * @param string $value
     * @return string
     */

    public static function detect($value) {
        if(is_array($value) || is_object($value)) {
            return self::ARR;
        }

        $value = trim($value);

        if(is_numeric($value)) {
            if(is_int($value + 0)) {
                return self::INTEGER;
            }

            return self::FLOAT;
        }

        if(strtotime($value) !== false) {
            return self::TIMESTAMP;
        }

        return self::STRING;
    }
}

?>