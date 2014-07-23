<?php

class tad_Arr
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
            $camelBackKey = tad_Str::camelBack($key);
            $buffer[$camelBackKey] = $value;
        }
        return $buffer;
    }
    
    /**
     * Remove an element from a numerical array
     *
     * @param  array $arr   The array to remove the value from
     * @param  mixed $value The value to remove from the array
     *
     * @return array        The original array minus the removed value
     */
    public static function remove(array $arr, $value)
    {
        return array_values(array_diff($arr, array(
            $value
        )));
    }
    
    /**
     * Remove key/value pairs on a key base from an array.
     *
     * Usage is tad_Arr::removeKey($arr, 'one', 'two')
     *
     * @return array The array minus the removed elements
     */
    public static function removeKey()
    {
        $args = func_get_args();
        $arr = $args[0];
        $keys = array_slice($args, 1);
        
        foreach ($arr as $k => $v) {
            if (in_array($k, $keys)) unset($arr[$k]);
        }
        return $arr;
    }
    
    /**
     * Remove key/value pairs from an array on a value base from an array.
     *
     * Usage is tad_Arr::removeValue($arr, 'one', 'two')
     *
     * @return array The array minus the removed elements
     */
    public static function removeValue()
    {
        $args = func_get_args();
        $arr = $args[0];
        $values = array_slice($args, 1);
        
        foreach ($arr as $k => $v) {
            if (in_array($v, $values)) unset($arr[$k]);
        }
        return $arr;
    }
}
