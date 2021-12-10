# :palm_tree: Palmtree Easy Collection

[![License](http://img.shields.io/packagist/l/palmtree/easy-collection.svg)](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/palmtreephp/collection/badges/build.png?b=master)](https://scrutinizer-ci.com/g/palmtreephp/collection/build-status/master)

Simple collection library which provides commonly used functionality missing from arrays.

## Requirements
* PHP >= 7.4

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/easy-collection
```

## Usage

```php
<?php

$collection = new \Palmtree\EasyCollection\Collection(['foo' => 'bar', 'baz' => 'qux']);

$foo = $collection->get('foo');

$foo = $collection->find(fn($v) => $v === 'bar');

$collection->set('baz2', 'qux');

$quxCollection = $collection->filter(fn($v) => $v === 'qux');

$collection->remove('baz');
```

Many other methods are provided. Read through the documented source code to see more.

## License

Released under the [MIT license](LICENSE)
