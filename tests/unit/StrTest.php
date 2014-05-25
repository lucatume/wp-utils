<?php
use tad\utils\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{

    public function VariousInputsProvider()
    {
        // $variousInputs
        return array(
         array('some-word', 'some-word', 'some_word', 'SomeWord', 'someWord', 'Some_Word'),
         array('some', 'some', 'some', 'Some', 'some', 'Some'),
         array('some_word', 'some-word', 'some_word', 'SomeWord', 'someWord', 'Some_Word'),
         array('some word', 'some-word', 'some_word', 'SomeWord', 'someWord', 'Some_Word'),
         array('SomeWord', 'some-word', 'some_word', 'SomeWord', 'someWord', 'Some_Word'),
         array('someWord', 'some-word', 'some_word', 'SomeWord', 'someWord', 'Some_Word'),
         array('some 23 word', 'some-23-word', 'some_23_word', 'Some23Word', 'some23Word', 'Some_23_Word'),
         array('some-23-word', 'some-23-word', 'some_23_word', 'Some23Word', 'some23Word', 'Some_23_Word'),
         array('some_23_word', 'some-23-word', 'some_23_word', 'Some23Word', 'some23Word', 'Some_23_Word'),
         array('some23Word', 'some-23-word', 'some_23_word', 'Some23Word', 'some23Word', 'Some_23_Word'),
         array('Some23Word', 'some-23-word', 'some_23_word', 'Some23Word', 'some23Word', 'Some_23_Word')
         );
    }

    /**
     * @dataProvider VariousInputsProvider
     */
    public function testHyphenWorksForVariousInputs($original, $hyphen, $underscore, $camelCase, $camelBack, $ucUnderscore)
    {
        $exp = $hyphen;
        $actual = Str::hyphen($original);
        $this->assertEquals($exp, $actual);    
    }

    /**
     * @dataProvider VariousInputsProvider
     */
    public function testUnderscoreWorksForVariousInputs($original, $hyphen, $underscore, $camelCase, $camelBack, $ucUnderscore)
    {
        $exp = $underscore;
        $actual = Str::underscore($original);
        $this->assertEquals($exp, $actual);    
    }
    /**
     * @dataProvider VariousInputsProvider
     */
    public function testCamelCaseWorksFroVariousInputs($original, $hyphen, $underscore, $camelCase, $camelBack, $ucUnderscore)
    {
        $exp = $camelCase;
        $actual = Str::camelCase($original);
        $this->assertEquals($exp, $actual);    
    }
    /**
     * @dataProvider VariousInputsProvider
     */
    public function testCamelBackWorksFroVariousInputs($original, $hyphen, $underscore, $camelCase, $camelBack, $ucUnderscore)
    {
        $exp = $camelBack;
        $actual = Str::camelBack($original);
        $this->assertEquals($exp, $actual);    
    }
    /**
     * @dataProvider VariousInputsProvider
     */
    public function testUcUnderscoreWorksFroVariousInputs($original, $hyphen, $underscore, $camelCase, $camelBack, $ucUnderscore)
    {
        $exp = $ucUnderscore;
        $actual = Str::ucfirstUnderscore($original);
        $this->assertEquals($exp, $actual);    
    }

    public function SomeWordsProvider()
    {
        // $someWords
        $buf = array('some', 'word', 'to', 'path');
        $end = 'some/word/to/path';
        $seps = array(' ', '-', '_');
        $ret = array();
        foreach ($seps as $sep) {
           $toAdd = array(implode($sep, $buf), $end);
           array_push($ret, $toAdd);
        }
        return $ret;
    }

    /**
     * @dataProvider SomeWordsProvider
     */
    public function testWillConvertToPathForSomeWords($start, $end)
    {
       $this->assertEquals($end, Str::toPath($start));
    }
}
