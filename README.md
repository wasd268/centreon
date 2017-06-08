# Centreon - IT and Application monitoring software #

## Introduction ##

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

## Download / Install ##

The fastest way to install up-to-date software from Centreon is to use
our [Centreon Enterprise Server](https://www.centreon.com/en/products/centreon-enterprise-server/)
Linux distribution, which comes with our software already packaged.

Latest source releases can be retrieved from [Centreon download center](https://download.centreon.com).
They can be installed by following the [online installation guide](https://documentation.centreon.com/docs/centreon/en/latest/installation/from_sources.html).

## Bug report / Feature request ##

Bug reports and feature requests are more than welcome. However if you
wish to open a new issue, please read [this page](project/issues.md)
first.


## Coding Style Guide ##

For these projects, Centreon work on follow the [PSR-2](http://www.php-fig.org/psr/psr-2/) coding style guidelines.

### Summary ### 
**Frequently used**

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
        ($a == $b)
        && ($b == $c)
        || ($c == $d)
    ) {
        $a = $d;
    }
}
```

### Check your code ###

PHP_CodeSniffer is available with composer:
```bash
$ php composer.phar require --dev \ squizlabs/php_codesniffer:"*@stable"
```
To validate the code with the PSR-2 standard:
```bash
$ ./bin/phpcs -p --standard=PSR2 src/centreon/myFile
```




## Authors ##

### Project leaders ###
* Julien Mathis
* Romain Le Merlus

### Dev team ###
* Lionel Assepo
* Maximilien Bersoult
* Kevin Duret
* Loic Laurent
* Rabaa Ridene
* Remi Werquin
* Quentin Garnier