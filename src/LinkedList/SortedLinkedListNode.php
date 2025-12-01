<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections\LinkedList;

use InvalidArgumentException;
use Stringable;

use function gettype;

/**
 * Node for a doubly-linked sorted list.
 *
 * Represents a single element in the linked list, storing its value and links to previous/next nodes.
 * Used internally by SortedLinkedList.
 *
 * @template T of int|string
 */
class SortedLinkedListNode implements Stringable {
    /**
     * Reference to the next node in the list, or null if this is the tail.
     *
     * @var null|SortedLinkedListNode<T>
     */
    public private(set) ?self $next = null;

    /**
     * Reference to the previous node in the list, or null if this is the head.
     *
     * @var null|SortedLinkedListNode<T>
     */
    public private(set) ?self $prev = null;

    /**
     * Create a new node with the given value.
     *
     * @param T $value The value to store in the node
     */
    public function __construct(
        /** @var T */
        public readonly int|string $value
    ) {}

    /**
     * Get a string representation of the node.
     */
    public function __toString(): string {
        return "Node({$this->value})";
    }

    /**
     * Check if this node has a next node.
     *
     * @return bool True if next node exists
     */
    public function hasNext(): bool {
        return $this->next !== null;
    }

    /**
     * Check if this node has a previous node.
     *
     * @return bool True if previous node exists
     */
    public function hasPrev(): bool {
        return $this->prev !== null;
    }

    /**
     * Detach this node from its neighbors in the list.
     * Updates adjacent nodes' pointers and clears own links.
     */
    public function detach(): void {
        if ($this->prev !== null) {
            $this->prev->next = $this->next;
        }
        if ($this->next !== null) {
            $this->next->prev = $this->prev;
        }
        $this->prev = null;
        $this->next = null;
    }

    /**
     * Compare this node's value to another node's value.
     *
     * @param SortedLinkedListNode<T> $other Node to compare against
     *
     * @return int -1 if less, 0 if equal, 1 if greater
     */
    public function compareTo(self $other): int {
        if (gettype($this->value) === 'string' && gettype($other->value) === 'string') {
            return strcmp($this->value, $other->value);
        }
        if ($this->value === $other->value) {
            return 0;
        }

        return $this->value < $other->value ? -1 : 1;
    }

    /**
     * Insert `$nodeToBeInserted` into the correct sorted position after nodeInList.
     *
     * @param self<T> $nodeToBeInserted Node to insert
     */
    public function insertSorted(self $nodeToBeInserted): void {
        if ($this->compareTo($nodeToBeInserted) > 0) {
            $this->insertBefore($nodeToBeInserted);
        } elseif ($this->next === null) {
            $this->insertAfter($nodeToBeInserted);
        } else {
            $this->next->insertSorted($nodeToBeInserted);
        }
    }

    /**
     * Inserts `$nodeToBeInserted` before `$this` in the linked list.
     *
     * @param self<T> $nodeToBeInserted Node to insert
     */
    private function insertBefore(self $nodeToBeInserted): void {
        if ($nodeToBeInserted->prev !== null || $nodeToBeInserted->next !== null) {
            throw new InvalidArgumentException('The element is already present in a list. Please detach it first.');
        }
        if ($this->prev !== null) {
            $this->prev->next = $nodeToBeInserted;
            $nodeToBeInserted->prev = $this->prev;
        }
        $nodeToBeInserted->next = $this;
        $this->prev = $nodeToBeInserted;
    }

    /**
     * Inserts `$nodeToBeInserted` after `$this` in the linked list.
     *
     * @param self<T> $nodeToBeInserted Node to insert
     */
    private function insertAfter(self $nodeToBeInserted): void {
        if ($nodeToBeInserted->prev !== null || $nodeToBeInserted->next !== null) {
            throw new InvalidArgumentException('The element is already present in a list. Please detach it first.');
        }
        if ($this->next !== null) {
            $this->next->prev = $nodeToBeInserted;
            $nodeToBeInserted->next = $this->next;
        }
        $nodeToBeInserted->prev = $this;
        $this->next = $nodeToBeInserted;
    }
}
