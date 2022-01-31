# :palm_tree: Palmtree Easy Collection

[![License](https://img.shields.io/github/license/palmtreephp/easy-collection)](LICENSE)
[![Build](https://img.shields.io/github/workflow/status/palmtreephp/easy-collection/Build.svg)](https://github.com/palmtreephp/easy-collection/actions/workflows/build.yml)

Immutable collections which provide commonly used functionality missing from arrays.

## Requirements

* PHP >= 8.0

## Installation

Use composer to add the package to your dependencies:

```bash
composer require palmtree/easy-collection
```

## Usage

Collections can be used just like arrays for the most part. They implement `ArrayAccess`, `IteratorAggregate` and `Countable`:

```php
use Palmtree\EasyCollection\Collection

$collection = new Collection(['foo' => 'bar', 'baz' => 'qux']);

$foo = $collection['foo'];
$collection['baz2'] = 'qux';

isset($collection['baz']);

// find returns the first matching element
$foo = $collection->find(fn ($v) => $v === 'bar');

// filter returns a new filtered collection
$quxCollection = $collection->filter(fn ($v) => $v === 'qux');

unset($collection['baz']);

count($collection);

foreach ($collection as $key => $value) {
    // do stuff with value and/or key
}
```

```php
use Palmtree\EasyCollection\Collection

$collection = Collection::create([1, 9, 5, 3, 7, 10])
    ->sort()
    ->filter(fn ($i) $i < 10)
    ->values()

$isAllOdd = $collection->every(fn ($i) => $i % 2 !== 0); // returns true
$collection->contains(10); // returns false as it was removed in our filter
```

Many other methods are provided. Read through the documented [source code](src/Collection.php) to see more.

## License

Released under the [MIT license](LICENSE)
