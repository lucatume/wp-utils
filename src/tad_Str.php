<?php

class tad_Str
{
    public static function underscore($string)
    {
        return self::uniteUsing('_', $string, -1);
    }
    public static function hyphen($string)
    {
        return self::uniteUsing('-', $string, -1);
    }
    public static function camelCase($string)
    {
        $buffer = self::extractComponentsFrom($string);

        // print_r($buffer);
        $buffer = implode(' ', $buffer);
        $buffer = ucwords($buffer);
        $buffer = preg_replace('/\s+/', '', $buffer);
        return $buffer;
    }
    public static function camelBack($string)
    {
        return lcfirst(self::camelCase($string));
    }
    protected static function uniteUsing($glue, $string, $flag = 0)
    {
        $elements = self::extractComponentsFrom($string);
        array_walk($elements,array(__CLASS__, 'joinWalk') , $flag);
        return implode($glue, $elements);
    }
    protected static function joinWalk (&$str, $key, $flag)
    {
        switch ($flag) {
        case 1:
            $str = ucfirst($str);
            break;

        case -1:
            $str = strtolower($str);
            break;

        case 11:
            $str = strtoupper($str);
            break;

        default:
            $str = $str;
            break;
        }
    }
    protected static function extractComponentsFrom($string)
    {
        $elements = explode(' ', preg_replace('/[-_\s\\\\\/]/', ' ', $string));

        // split elements further by uppercase and numbers
        $smallerElements = array();
        foreach ($elements as $el) {
            $result = trim(preg_replace("/([A-Z]?[a-z]+|[0-9]+)/", " $1", $el));
            $smallerElements = array_merge($smallerElements, explode(' ', $result));
        }
        return $smallerElements;
    }
    public static function ucfirstUnderscore($string)
    {
        return self::uniteUsing('_', $string, 1);
    }
    public static function toPath($string)
    {
        return self::uniteUsing(DIRECTORY_SEPARATOR, $string);
    }
    private static function getTagLenghtIn($word)
    {
        $tagsLength = 0;
        $tags = array();
        preg_match("/<[^>]*>/", $word, $tags);
        isset($tags[0]) ? $tagsLength = strlen($tags[0]) : $tagsLength = 0;
        return $tagsLength;
    }
    public static function atMostChars($string, $maxChars = 300, $ellipsis = '&hellip;')
    {
        $string = self::replaceSpacesInsideTagsWith($string, '&nbsp;');
        $words = explode(' ', $string);
        $words = array_reverse($words);
        $buffer = array_pop($words) . ' ';
        $tagsLength = self::getTagLenghtIn($buffer);
        while (strlen(trim($buffer) - $tagsLength) <= $maxChars) {
            $next = array_pop($words);
            $buffer.= $next . ' ';
            $tagsLength = self::getTagLenghtIn($next);
        }
        $out = trim($buffer);
        if (count($words) > 0) {
            $out.= $ellipsis;
        }

        // replace spacer with space
        $out = preg_replace('/&nbsp;/', ' ', $out);
        return $out;
    }

    public static function splitLinesByWords($string, $wordsPerLine = 10, $tag = 'span', $class = 'line', $maxChars = null, $ellipsis = '&hellip;')
    {
        if ($maxChars) {
            $string = self::atMostChars($string, $maxChars, $ellipsis);
        }

        // split the string by new lines
        $paragraphs = explode('\n', $string);
        $out = array();
        $openTag = sprintf('<%s class="%s">', $tag, $class);
        $closeTag = sprintf('</%s>', $tag);

        foreach ($paragraphs as $paragraph) {
            if (strlen($paragraph) == 0) {
                continue;
            }
            $words = explode(' ', $paragraph);
            $words = array_reverse($words);
            $buffer = array();
            while (count($words) > 0) {
                $next = array_pop($words);
                if (count($buffer) + 1 <= $wordsPerLine) {
                    array_push($buffer, $next);
                } else {
                    array_push($words, $next);
                    array_push($out, $openTag . implode(' ', $buffer) . $closeTag);

                    // $out.= $openTag . implode(' ', $buffer) . $closeTag;
                    $buffer = array();
                }
            }

            // $out.= $openTag . implode(' ', $buffer) . $closeTag;
            array_push($out, $openTag . implode(' ', $buffer) . $closeTag);
        }
        return $out;
    }
    public static function splitLinesByChars($string, $charsPerLine = 80, $tag = 'span', $class = 'line', $maxChars = null, $ellipsis = '&hellip;')
    {
        if ($maxChars) {
            $string = self::atMostChars($string, $maxChars, $ellipsis);
        }

        // split the string by new lines
        $paragraphs = explode('\n', $string);
        $out = array();
        $openTag = sprintf('<%s class="%s">', $tag, $class);
        $closeTag = sprintf('</%s>', $tag);
        foreach ($paragraphs as $paragraph) {
            if (strlen($paragraph) == 0) {
                continue;
            }

            // split the paragraph into words
            $words = explode(' ', $paragraph);
            $words = array_reverse($words);
            $buffer = '';
            $matches = array();
            while (count($words) > 0) {
                $next = array_pop($words);
                $tagsLength = self::getTagLenghtIn($next);
                $futureBufferLength = strlen($buffer . $next) - $tagsLength;
                if ($futureBufferLength <= $charsPerLine || $buffer == '') {
                    $buffer.= $next . ' ';
                } else {
                    array_push($words, $next);

                    // $out.= sprintf('%s%s%s', $openTag, trim($buffer), $closeTag);
                    array_push($out, sprintf('%s%s%s', $openTag, trim($buffer), $closeTag));
                    $buffer = '';
                }
            }

            // add the last buffer
            // $out.= sprintf('%s%s%s', $openTag, trim($buffer), $closeTag);
            array_push($out, sprintf('%s%s%s', $openTag, trim($buffer), $closeTag));
        }
        return $out;
    }

    private static function replaceSpacesInsideTagsWith($string, $replaceWith = '&nbsp;')
    {
        $words = implode(' <', explode('<', $string));
        $words = implode('> ', explode('>', $words));
        $words = preg_replace('/\\s+/', ' ', $words);
        $words = explode(' ', $string);

        $buffer = array();
        $inTag = false;
        $tagBuffer = array();
        foreach ($words as $word) {
            if (preg_match("/<[^\s]*>/", $word)) {

                // open and closing tag like '<span>' or '</span>'
                array_push($buffer, $word);
            } else if (preg_match("/<[^\\s]*/", $word)) {

                // opening a tag
                $inTag = true;
                array_push($tagBuffer, $word);
            } else if (preg_match("/[^\\s]*>/u", $word)) {

                // // closing a tag
                $inTag = false;
                array_push($tagBuffer, $word);
                array_push($buffer, implode($replaceWith, $tagBuffer));
                $tagBuffer = array();
            } else {

                // a word...
                if ($inTag) {
                    array_push($tagBuffer, $word);
                } else {
                    array_push($buffer, $word);
                }
            }
        }
        return implode(' ', $buffer);
    }
}

// Add function lcfirst missing from PHP 5.2
if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}
