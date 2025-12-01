<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections\LinkedList;

use Iterator;
use MaxaOndrej\ShipMonk\Collections\IListIterator;
use Stringable;

use function count;

/**
 * Iterator for traversing a SortedLinkedList.
 *
 * Provides forward and reverse iteration over the elements of a sorted linked list.
 * Used internally by SortedLinkedList.
 *
 * @template T of int|string
 *
 * @implements IListIterator<T>
 */
class SortedLinkedListIterator implements Stringable, IListIterator {
    /**
     * Current position in the elements array.
     */
    private int $position = 0;

    /**
     * Construct a new iterator for the given elements.
     *
     * @param array<T> $elements Elements to iterate
     */
    public function __construct(
        private readonly array $elements,
    ) {}

    /**
     * Get the string representation of the iterator.
     */
    public function __toString(): string {
        return "SortedLinkedListIterator(position={$this->position})";
    }

    /**
     * Get the current element.
     *
     * @return T
     */
    public function current(): mixed {
        return $this->elements[$this->position];
    }

    /**
     * Move to the next element.
     */
    public function next(): void {
        ++$this->position;
    }

    /**
     * Get the current position key.
     */
    public function key(): int {
        return $this->position;
    }

    /**
     * Check if the current position is valid.
     *
     * @return bool True if valid
     */
    public function valid(): bool {
        return count($this->elements) > $this->position;
    }

    /**
     * Rewind to the first element.
     */
    public function rewind(): void {
        $this->position = 0;
    }
}
