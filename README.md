# :palm_tree: Palmtree Easy Collection

[![License](http://img.shields.io/packagist/l/palmtree/easy-collection.svg)](LICENSE)
[![Build](https://img.shields.io/github/workflow/status/palmtreephp/easy-collection/Build.svg)](https://github.com/palmtreephp/easy-collection/actions/workflows/build.yml)

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
use Palmtree\EasyCollection\Collection

$collection = new Collection(['foo' => 'bar', 'baz' => 'qux']);

$foo = $collection->get('foo');
$collection->set('baz2', 'qux');

// find returns the first matching element
$foo = $collection->find(fn ($v) => $v === 'bar');

// filter returns a new filtered collection
$quxCollection = $collection->filter(fn ($v) => $v === 'qux');

$collection->remove('baz');
```

Many other methods are provided. Read through the documented source code to see more.

## License

Released under the [MIT license](LICENSE)
