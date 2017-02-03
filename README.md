
Phug Compiler
===========

What is Phug Compiler?
--------------------

The Phug compiler get a document node from the parser then return the compiled
document element to be formatter to HTML, XML or any other representation with
the formatter.

Installation
------------

Install via Composer

```bash
composer require phug/compiler
```

Usage
-----

```php

$compiler = new Phug\Compiler($options);
$root = $compiler->compile($pugInput);

//$root is now a Phug\Formatter\Element\DocumentElement element
```
