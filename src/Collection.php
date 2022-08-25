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
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->elements[] = $element;

        return $this;
    }

    /**
     * Removes the element with the given key from the collection and returns it.
     *
     * @param TKey $key
     *
     * @return T|null
     */
    public function remove($key)
    {
        if (!$this->containsKey($key)) {
            return null;
        }

        $removed = $this->elements[$key];

        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * Removes the given element from the collection and returns whether it existed or not.
     *
     * @param T $element
     */
    public function removeElement($element): bool
    {
        $key = $this->key($element);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
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
     * Returns the given element's key if it is present within the collection or false otherwise.
     *
     * @param T $element
     *
     * @return TKey|false
     */
    public function key($element)
    {
        return array_search($element, $this->elements, true);
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
     * @param ?callable(T, TKey=):bool $predicate
     *
     * @return Collection<TKey,T>
     *
     * @psalm-suppress PossiblyNullArgument
     */
    public function filter(?callable $predicate = null): self
    {
        if ($predicate === null) {
            return new self(array_filter($this->elements));
        }

        return new self(array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Returns a new collection whose values are mapped by the callback function.
     *
     * @param callable(T, TKey=):U $callback
     *
     * @return Collection<TKey, U>
     *
     * @template U
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
     * @param ?callable(T, T):int $comparator
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
     * @param ?callable(T, T):int $comparator
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
    public function clear(): self
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Returns whether the collection is a list as per array_is_list.
     */
    public function isList(): bool
    {
        return $this->isEmpty() || $this->elements === array_values($this->elements);
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
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @param TKey $offset
     *
     * @return T
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T         $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }
}
