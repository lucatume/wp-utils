# WordPress utilities

A small set of utilities to speed up WordPress development.

## Script
A utility class that will make dealing with script and style suffix a breeze. Depending on the <code>SCRIPT_DEBUG</code> constant it will try to enqueue the minified or non minified version of the script or style.

    $path = get_stylesheet_directory_uri() . '/assets/css/theme_style.css';
    $debugDependantPath = Script::suffix($path);

    wp_enqueue_style('theme-style', $debugDependantPath);

## Str(ings)
Will make string conversion to go from one naming convention to another.

    $string = 'some_name_here';

    // someNameHere
    $out = Str::camelBack($string);
    // some-name-here
    $out = Str::hyphen($string);
    // some_name_here
    $out = Str::underscore($string);
    // SomeNameHere
    $out = Str::camelCase($string);
    // some/name/here (some\name\here on Win)
    $out = Str::toPath($string);

it also packs some methods to split strings into lines on a char or word basis with html tags preservation.
    
    $in = "lorem ipsum some other stuff here"
    
    // lorem ipsum some other...
    $out = Str::atMostChars($in, 25, '...');

    // <span class="line">lorem ipsum</span>
    // <span class="line">some other</span>
    // <span class="line">stuff here</span>
    $out = Str::splitLinesByWords($in, 2);

    $in = 'some words are longer than others'

    // <span class="line">some words are longer</span>
    // <span class="line">than others</span>
    $out = Str::splitLinesByChars($in, 25);

## Arr(ays)
Array utilities.

## JsObject
A quasi WordPress specific set of functions meant to make printing JavaScript objects on the page easy. The class will take care to print objects containing callback functions too.

    $in = array(
        'value' => 'hello there',
        'callback' => 'function(){alert("hello there");}'
        );
    
    $out = JsObject::on($in)->getOut();

    // or print on the page in WordPress
    JsObject::on($in)->localize();
