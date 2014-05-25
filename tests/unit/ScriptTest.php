<?php
use tad\utils\Script;

class ScriptTest extends \PHPUnit_Framework_TestCase
{

    public function DifferentNonMinifiedFileTypesProvider()
    {
        $url = 'http://some.url/assets';
        // $differentFileTypes
        return array(
            array($url . '/js/script.js', $url . '/js/script.js', $url . '/js/script.min.js'),
            array($url . '/js/script.min.js', $url . '/js/script.js', $url . '/js/script.min.js'),
            array($url . '/css/style.css', $url . '/css/style.css' , $url . '/css/style.min.css'),
            array($url . '/css/style.min.css', $url . '/css/style.css', $url . '/css/style.min.css'),
           );
    }

    /**
     * @dataProvider DifferentNonMinifiedFileTypesProvider
     */
    public function testSuffixWillWorkForDifferentFileTypesInDebugMode($start, $normal, $minified)
    {
        $this->assertEquals($normal, Script::suffix($start, false));
    }
    /**
     * @dataProvider DifferentNonMinifiedFileTypesProvider
     */
    public function testSuffixWillWorkForDifferentFileTypesInMinified($start, $normal, $minified)
    {
        $this->assertEquals($minified, Script::suffix($start, true));
    }
}
