#Centreon - IT and Application monitoring software

##Introduction

Centreon is one of the most flexible and powerful monitoring softwares
on the market; it is absolutely free and Open Souce (released under GNU
General Public License version 2, see LICENSE file).

This software requires [Centreon Engine](https://github.com/centreon/centreon-engine)
and [Centreon Broker](https://github.com/centreon/centreon-broker) to be
operational.

**Quick links**
* the official [Centreon (company) website](https://www.centreon.com)
* the official [online documentation](https://documentation.centreon.com)
* our [bugtracker](https://github.com/centreon/centreon/issues)
* the [forum](http://forum.centreon.com)
* the [download center](https://download.centreon.com)

##Download / Install

The fastest way to install up-to-date software from Centreon is to use
our [Centreon Enterprise Server](https://www.centreon.com/en/products/centreon-enterprise-server/)
Linux distribution, which comes with our software already packaged.

Latest source releases can be retrieved from [Centreon download center](https://download.centreon.com).
They can be installed by following the [online installation guide](https://documentation.centreon.com/docs/centreon/en/latest/installation/from_sources.html).

##Bug report / Feature request

Bug reports and feature requests are more than welcome. However if you
wish to open a new issue, please read [this page](project/issues.md)
first.


##Coding Style Guide

###PHP

For these projects, Centreon work on follow the [PSR-2](http://www.php-fig.org/psr/psr-2/) coding style guidelines.

**Summary**

* Code must use an indent of 4 spaces, and must not use tabs for indenting.
* There must not be trailing whitespace at the end of non-blank lines.
* The PHP constants true, false, and null must be in lower case.
* For control structures( if/for/while…), the placement of parentheses, spaces, and braces; and that else and elseif are on the same line as the closing brace from the earlier body.-b 
* The keyword elseif should be used instead of else if so that all control keywords look like single words.

```php
namespace Vendor\Package;

use FooInterface;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class Foo extends Bar implements FooInterface
{
    public function sampleMethod($a, $b = null)
    {
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
 
        foreach ($iterable as $key => $value) {
             // foreach body
        }
 
        echo 'A string with ' . $someVariable . ' and ' . $otherVariable;
    }

    final public static function bar()
    {
        // method body
    }
}

```

* The limit on line length must be 120 characters.
* Method and variable names must be in camelCase.
* For the casting, please use (int)$var instead of intval($var) method.

```php
public function longLine(
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) {
       
    $longArray =array(
        array(
            0,
            1,
            2
        ),
        3,
        4
    );
 
 
    $longString = 'Some String with ' . (string)$someVariable . ' and ' .
        'Concatinated';
 
    if (
        ($a == $b) &&
        ($b == $c) ||
        ($c == $d)
    ) {
        $a = $d;
    }
}
```

####Check your code

To check your code, you can use [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer), it is available with composer:
```bash
$ php composer.phar require --dev \ squizlabs/php_codesniffer:"*@stable"
```
To validate the code with the [PSR-2](http://www.php-fig.org/psr/psr-2/) standard:
```bash
$ ./bin/phpcs -p --standard=PSR2 src/centreon/myFile
```

###HTML

All tags and attributes are lowercase.

###CSS

Definition ideally as dashed name:
    class: .some-class-name
    id: #some-id-to-an-element

Both with lowercase characters (although classes are not case-sensitive, id's are!), the separator is minus [-]. You can use underscore [_] if it makes the separation of the identifier and the record id easier. E.g. my-id_33. It will become necessary to do so if you use UUIDs (which contain minus chars).

```css
span.success {
    color: green;
}
```
###JS

* Method and variable names must be in camelCase.
```js
firstName = "John";
```
* Arrays that span across multiple lines can have a trailing comma to make sure that adding new rows does not change the previous row, as well.
```js
var myTab = [];
var myTab = new Array();

myTab = [
	'first',
	'second'
];

var tabAsso={
    "val1":10,
    "val2":55,
    "val3":30
};
```
* Use the else if statement to specify a new condition if the first condition is false.
```js
if (time < 10) {
    greeting = "Good morning";
} else if (time < 20) {
    greeting = "Good day";
} else {
    greeting = "Good evening";
}
```
* Put spaces around operators ( = + - * / ), and after commas.
```js
var x = y + z;
var values = [1, 2, 3]; 

for (i = 0; i < 5; i++) {
    x += i;
}
```
* Use 4 spaces for indentation of code blocks.
```js
function toCelsius(fahrenheit) {
    return (5 / 9) * (fahrenheit - 32);
}
```
* Line Length < 80
```js
document.getElementById("world").innerHTML =
    "Hello World.";
```
* Declarations on Top
```js
// Declare at the beginning
var firstName, lastName;

// Use later
firstName = "John";
lastName = "Doe";
```
* Declarations on Top
```js
// Declare and initiate at the beginning
var firstName = "",
    price = 0,
    myArray = [],
    myObject = {}; 
```
* Reduce Activity in Loops
```js
// Declare and initiate at the beginning
for (i = 0; i < arr.length; i++) {}
//became
var i;
var l = arr.length;
for (i = 0; i < l; i++) {}
```
* Avoid Unnecessary Variables
```js
// Declare and initiate at the beginning
var fullName = firstName + " " + lastName;
document.getElementById("name").innerHTML = fullName;
//became
document.getElementById("name").innerHTML = firstName + " " + lastName 
```



##Authors

###Project leaders
* Julien Mathis
* Romain Le Merlus

###Dev team
* Lionel Assepo
* Maximilien Bersoult
* Kevin Duret
* Loic Laurent
* Rabaa Ridene
* Remi Werquin
* Quentin Garnier