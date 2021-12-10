<?php

declare(strict_types=1);

namespace Palmtree\EasyCollection\Test;

use Palmtree\EasyCollection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1]);
        $collection->add($obj2);

        $this->assertSame($obj2, $collection->last());
        $this->assertSame($obj1, $collection->get(0));
        $this->assertSame($obj2, $collection->get(1));
    }

    public function testCannotAddToNonList(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot add an element to a collection which is not a list. Use Palmtree\EasyCollection\Collection::set instead');

        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1]);

        $collection->add($obj2);
    }

    public function testSet(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1]);
        $collection->set('bar', $obj2);

        $this->assertSame($obj2, $collection->get('bar'));
    }

    public function testGet(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame($obj1, $collection->get('foo'));
        $this->assertSame($obj2, $collection->get('bar'));
    }

    public function testRemoveElement(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $collection->removeElement($obj1);

        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
    }

    public function testRemove(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $collection->remove('foo');

        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
    }

    public function testKeys(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame(['foo', 'bar'], $collection->keys()->toArray());
    }

    public function testValues(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame([$obj1, $obj2], $collection->values()->toArray());
    }

    public function testFirstKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame('foo', $collection->firstKey());

        $collection->clear();

        $this->assertNull($collection->firstKey());
    }

    public function testLastKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2, 'baz' => $obj3]);

        $this->assertSame('baz', $collection->lastKey());

        $collection->clear();

        $this->assertNull($collection->lastKey());
    }

    public function testFirst(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $this->assertSame($obj1, $collection->first());

        $collection->clear();

        $this->assertNull($collection->first());
    }

    public function testLast(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $collection = new Collection([$obj1, $obj2, $obj3]);

        $this->assertSame($obj3, $collection->last());

        $collection->clear();

        $this->assertNull($collection->last());
    }

    public function testFind(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = new Collection([$obj1, $obj2, $obj3]);

        $this->assertSame($obj2, $collection->find(fn ($o) => $o->foo === 'bar'));
        $this->assertNull($collection->find(fn ($o) => $o->foo === 'qux'));
    }

    public function testFilter(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = (new Collection([$obj1, $obj2, $obj3]))->filter(fn ($o) => $o->foo === 'bar');

        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
        $this->assertTrue($collection->contains($obj3));
    }

    public function testMap(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = (new Collection([$obj1, $obj2, $obj3]))->map(fn ($o) => $o->foo);

        $this->assertSame(['noop', 'bar', 'bar'], $collection->toArray());
    }

    public function testReduce(): void
    {
        $collection = new Collection([10, 20, 30]);

        $this->assertSame(60, $collection->reduce(fn ($val, $acc) => $val + $acc));
    }

    public function testContains(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $this->assertTrue($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
    }

    public function testContainsKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey('bar'));
    }

    public function testClearAndIsEmpty(): void
    {
        $collection = new Collection(['foo', 'bar']);

        $collection->clear();

        $this->assertTrue($collection->isEmpty());
    }

    public function testCount(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertCount(3, $collection);

        $collection->removeElement('bar');

        $this->assertCount(2, $collection);
    }

    public function testSome(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertTrue($collection->some(fn ($v) => $v === 'bar'));
        $this->assertFalse($collection->some(fn ($v) => $v === 'qux'));
    }

    public function testEvery(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertTrue($collection->every(fn ($v) => $v !== 'qux'));
        $this->assertFalse($collection->every(fn ($v) => $v === 'qux'));
    }

    public function testSort(): void
    {
        $collection = new Collection([3, 1, 2, 9, 7]);

        $collection->sort();

        $this->assertSame([1, 2, 3, 7, 9], $collection->values()->toArray());

        $collection->sort(fn ($a, $b) => $b <=> $a);

        $this->assertSame([9, 7, 3, 2, 1], $collection->values()->toArray());
    }

    public function testSorted(): void
    {
        $collection = new Collection([3, 1, 2, 9, 7]);
        $sortedCollection = $collection->sorted();

        $this->assertNotSame($collection, $sortedCollection);
        $this->assertSame([1, 2, 3, 7, 9], $sortedCollection->values()->toArray());
    }

    public function testArrayAccess(): void
    {
        $collection = new Collection();
        $collection['foo'] = 'bar';
        $collection['baz'] = 'qux';

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $collection->toArray());

        $this->assertTrue(isset($collection['foo']));

        unset($collection['foo']);

        $this->assertFalse($collection->containsKey('foo'));

        $this->assertSame('qux', $collection['baz']);
    }

    public function testIterator(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertSame(['foo', 'bar', 'baz'], iterator_to_array($collection));
    }
}
