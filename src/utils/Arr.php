<?php
namespace tad\utils;

use \tad\utils\Str;

class Arr
{
    public static function isAssoc($array)
    {
        if (!is_array($array)) {
            return false;
        }
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v) {
                return true;
            }
        }
        return false;
    }
    public static function camelBackKeys($arr, $default = null)
    {
        if (!self::isAssoc($arr)) {
            return $default;
        }
        $buffer = array();
        foreach ($arr as $key => $value) {
            $camelBackKey = Str::camelBack($key);
            $buffer[$camelBackKey] = $value;
        }
        return $buffer;
    }
}
