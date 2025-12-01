<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * Interface for a sorted linked list.
 *
 * Provides methods for adding, removing, and iterating elements in a sorted linked list.
 * Implementations must maintain elements in sorted order and enforce type safety.
 *
 * @template T
 *
 * @extends IteratorAggregate<int,T>
 * @extends ArrayAccess<int,T>
 */
interface IList extends IteratorAggregate, ArrayAccess, Countable {
    /**
     * Returns an iterator for traversing the list in descending order (from largest to smallest).
     *
     * @return IListIterator<T> Iterator over elements in reverse order
     */
    public function getIteratorReversed(): IListIterator;

    /**
     * Adds a single element to the list, maintaining sorted order.
     *
     * @param T $element The element to add
     */
    public function add($element): void;

    /**
     * Adds multiple elements to the list, maintaining sorted order.
     *
     * @param T ...$elements The elements to add
     */
    public function addAll(...$elements): void;

    /**
     * Removes the first occurrence of the specified element from the list.
     *
     * @param T $element The element to remove
     *
     * @return bool True if the element was found and removed, false otherwise
     */
    public function remove($element): bool;

    /**
     * Removes all occurrences of the specified element from the list.
     *
     * @param T $element The element to remove
     *
     * @return bool True if any elements were removed, false otherwise
     */
    public function removeAll($element): bool;
}
