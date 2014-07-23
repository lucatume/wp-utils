<?php
/**
 * A utility class dealing with scripts and styles files and paths operations.
 */
class tad_Script
{
    /**
     * Properly inserts a suffix in a file path taking user inputs or debug variables into account.
     *
     * @param  string $path   The url to the file to suffix
     * @param  bool $minify A flag that allows overriding environment variabels and will force suffixing behavior; true will append the '.min' suffi to the files like 'script.min.js'; false will remove or not append the suffix. Default behaviour will check for debug variables.
     *
     * @return strin         The suffixed url.
     */
    public static function suffix($path, $minify = null)
    {
        $buffer = array();
        preg_match("/(\\.\\w*)$/um", $path, $buffer);
        $extension = $buffer[1];
        preg_match("/^(.*\\/[\\w-_]*)/um", $path, $buffer);
        $fileName = $buffer[1];
        $suffix = '';
        if (is_null($minify)) {
            if (!defined("SCRIPT_DEBUG") or !SCRIPT_DEBUG) {
                $suffix = '.min';
            }
        } else {
            $minify ? $suffix = '.min' : $suffix = '';
        }
        return $fileName . $suffix . $extension;
    }
}
