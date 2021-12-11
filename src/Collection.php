<?php

declare(strict_types=1);

namespace Palmtree\EasyCollection;

/**
 * @template TKey of array-key
 * @template T
 *
 * @template-implements \IteratorAggregate<TKey,T>
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array<TKey,T>
     */
    private array $elements = [];

    /**
     * @param iterable<TKey,T> $elements
     */
    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $key => $element) {
            $this->elements[$key] = $element;
        }
    }

    /**
     * Returns the element with the given key.
     *
     * @param TKey $key
     *
     * @return T
     */
    public function get($key)
    {
        return $this->elements[$key];
    }

    /**
     * Sets the given element to the given key in the collection.
     *
     * @param TKey $key
     * @param T    $element
     */
    public function set($key, $element): self
    {
        $this->elements[$key] = $element;

        return $this;
    }

    /**
     * Adds the given element onto the end of the collection.
     *
     * @param T $element
     */
    public function add($element): self
    {
        if (!$this->isList()) {
            throw new \LogicException(sprintf('Cannot add an element to a collection which is not a list. Use %s::%s instead', __CLASS__, 'set'));
        }

        $lastKey = (int)$this->lastKey();

        /** @var TKey $key */
        $key = $lastKey + 1;

        $this->elements[$key] = $element;

        return $this;
    }

    /**
     * Removes the element with the given key.
     *
     * @param TKey $key
     *
     * @return Collection<TKey,T>
     */
    public function remove($key): self
    {
        unset($this->elements[$key]);

        return $this;
    }

    /**
     * Removes the given element.
     *
     * @param T $element
     *
     * @return Collection<TKey,T>
     */
    public function removeElement($element): self
    {
        $key = array_search($element, $this->elements, true);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * Returns a new collection containing the current collection's keys.
     *
     * @return Collection<int, TKey>
     */
    public function keys(): self
    {
        return new self(array_keys($this->elements));
    }

    /**
     * Returns a new collection containing the current collection's values.
     *
     * @return Collection<int, T>
     */
    public function values(): self
    {
        return new self(array_values($this->elements));
    }

    /**
     * Returns the first key in the collection.
     *
     * @return TKey|null
     */
    public function firstKey()
    {
        return array_key_first($this->elements);
    }

    /**
     * Returns the last key in the collection.
     *
     * @return TKey|null
     */
    public function lastKey()
    {
        return array_key_last($this->elements);
    }

    /**
     * Returns the first element in the collection.
     *
     * @return T|null
     */
    public function first()
    {
        $firstKey = $this->firstKey();

        if ($firstKey === null) {
            return null;
        }

        return $this->elements[$firstKey];
    }

    /**
     * Returns the last element in the collection.
     *
     * @return T|null
     */
    public function last()
    {
        $lastKey = $this->lastKey();

        if ($lastKey === null) {
            return null;
        }

        return $this->elements[$lastKey];
    }

    /**
     * Returns whether the given element exists in the collection.
     *
     * @param T $element
     */
    public function contains($element): bool
    {
        return \in_array($element, $this->elements, true);
    }

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param TKey $key
     */
    public function containsKey($key): bool
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * Returns the first matching element which passes the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     *
     * @return T|null
     */
    public function find(callable $predicate)
    {
        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Returns a new collection containing all elements which pass the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     *
     * @return Collection<TKey,T>
     */
    public function filter(callable $predicate): self
    {
        return new self(array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Returns a new collection whose values are mapped by the callback function.
     *
     * @param callable(T, TKey=):mixed $callback
     *
     * @return Collection<TKey, mixed>
     */
    public function map(callable $callback): self
    {
        $mapped = [];

        foreach ($this->elements as $key => $element) {
            $mapped[$key] = $callback($element, $key);
        }

        return new self($mapped);
    }

    /**
     * Reduces the collection a single value.
     *
     * @param callable(mixed, T):mixed $callback
     * @param mixed                    $initial
     *
     * @return mixed|null
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->elements, $callback, $initial);
    }

    /**
     * Returns whether at least one element passes the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     */
    public function some(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether all elements pass the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     */
    public function every(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sorts the collection in place with an optional comparator function.
     * If the comparator is omitted, the collection is sorted using SORT_REGULAR.
     *
     * @see https://www.php.net/manual/en/function.asort.php
     *
     * @param callable(T)|null $comparator
     *
     * @return Collection<TKey,T>
     */
    public function sort(?callable $comparator = null): self
    {
        if (!$comparator) {
            asort($this->elements);

            return $this;
        }

        uasort($this->elements, $comparator);

        return $this;
    }

    /**
     * Returns a new collection with the elements sorted.
     *
     * @param callable(T)|null $comparator
     *
     * @return Collection<TKey,T>
     */
    public function sorted(?callable $comparator = null): self
    {
        return (new self($this->elements))->sort($comparator);
    }

    /**
     * Returns whether the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Clears the collection.
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * Returns whether the collection is a list as per array_is_list.
     *
     * Credit: https://github.com/symfony/polyfill-php81
     */
    public function isList(): bool
    {
        if ($this->isEmpty() || $this->elements === array_values($this->elements)) {
            return true;
        }

        $nextKey = -1;

        foreach ($this->elements as $k => $v) {
            if ($k !== ++$nextKey) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * Returns the collection as a native array.
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return \ArrayIterator<TKey,T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }
}
