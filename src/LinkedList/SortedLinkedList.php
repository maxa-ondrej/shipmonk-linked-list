<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections\LinkedList;

use InvalidArgumentException;
use MaxaOndrej\ShipMonk\Collections\IList;
use Stringable;

use function gettype;

/**
 * A sorted doubly-linked list implementation for integers or strings.
 *
 * Maintains elements in ascending order and supports duplicates. Type safety is enforced at runtime.
 * Implements ArrayAccess, Countable, and IteratorAggregate for convenient usage.
 *
 * Example usage:
 * ```php
 * $list = SortedLinkedList::int(5, 1, 3);
 * echo $list[0]; // 1
 * echo count($list); // 3
 * foreach ($list as $value) { echo $value; }
 * ```
 *
 * @template T of int|string
 *
 * @implements IList<T>
 */
class SortedLinkedList implements Stringable, IList {
    /**
     * The first node in the list (smallest value).
     * Null if the list is empty.
     *
     * @var null|SortedLinkedListNode<T>
     */
    public private(set) ?SortedLinkedListNode $head = null;

    /**
     * The last node in the list (largest value).
     * Null if the list is empty.
     *
     * @var null|SortedLinkedListNode<T>
     */
    public private(set) ?SortedLinkedListNode $tail = null;

    /**
     * The total number of elements in the list.
     *
     * @var int<0,max>
     */
    private int $count = 0;

    /**
     * Private constructor. Use SortedLinkedList::int() or ::string() to create instances.
     *
     * @param string $type 'integer' or 'string'
     */
    private function __construct(private readonly string $type) {}

    /**
     * Get a string representation of the list.
     */
    public function __toString(): string {
        return "SortedLinkedList({$this->count}x {$this->type})";
    }

    /**
     * Create a sorted linked list for integers.
     *
     * @param int ...$values Initial values to add
     *
     * @return SortedLinkedList<int>
     */
    public static function int(...$values): self {
        /** @var SortedLinkedList<int> $list */
        $list = new self('integer');
        $list->addAll(...$values);

        return $list;
    }

    /**
     * Create a sorted linked list for strings.
     *
     * @param string ...$values Initial values to add
     *
     * @return SortedLinkedList<string>
     */
    public static function string(...$values): self {
        /** @var SortedLinkedList<string> $list */
        $list = new self('string');
        $list->addAll(...$values);

        return $list;
    }

    /**
     * Get an iterator for traversing the list in ascending order.
     *
     * @return SortedLinkedListIterator<T> Iterator over elements from smallest to largest
     */
    public function getIterator(): SortedLinkedListIterator {
        /** @var array<T> $elements */
        $elements = [];
        for ($node = $this->head; $node !== null; $node = $node->next) {
            $elements[] = $node->value;
        }

        return new SortedLinkedListIterator($elements);
    }

    /**
     * Get an iterator for traversing the list in descending order.
     *
     * @return SortedLinkedListIterator<T> Iterator over elements from largest to smallest
     */
    public function getIteratorReversed(): SortedLinkedListIterator {
        /** @var array<T> $elements */
        $elements = [];
        for ($node = $this->tail; $node !== null; $node = $node->prev) {
            $elements[] = $node->value;
        }

        return new SortedLinkedListIterator($elements);
    }

    /**
     * Check if an element exists at the given index (ArrayAccess).
     *
     * @param int $offset Index to check
     *
     * @return bool True if element exists
     */
    public function offsetExists(mixed $offset): bool {
        return $this->count > $offset;
    }

    /**
     * Get the element at the given index (ArrayAccess).
     *
     * @param int $offset Index to retrieve
     *
     * @return null|T Element at index or null if out of bounds
     */
    public function offsetGet(mixed $offset): mixed {
        return $this->getNode($offset)?->value;
    }

    /**
     * Not supported. Throws exception if called (ArrayAccess).
     *
     * @param int $offset
     * @param T   $value
     *
     * @throws InvalidArgumentException Always
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new InvalidArgumentException('This list type does not allow modification at a specific index.');
    }

    /**
     * Remove the element at the given index (ArrayAccess).
     *
     * @param int $offset Index to remove
     */
    public function offsetUnset(mixed $offset): void {
        $node = $this->getNode($offset);
        if ($node !== null) {
            $this->removeNode($node);
        }
    }

    /**
     * @return int Number of elements
     */
    public function count(): int {
        return $this->count;
    }

    /**
     * Add an element to the list, maintaining sorted order.
     *
     * @param T $element Value to add
     *
     * @throws InvalidArgumentException If type does not match
     */
    public function add($element): void {
        $element = $this->createNode($element);
        $this->head?->insertSorted($element);
        if (!$element->hasPrev()) {
            $this->head = $element;
        }
        if (!$element->hasNext()) {
            $this->tail = $element;
        }
        ++$this->count;
    }

    /**
     * Add multiple elements to the list, maintaining sorted order.
     *
     * @param T ...$elements Values to add
     *
     * @throws InvalidArgumentException If any type does not match
     */
    public function addAll(...$elements): void {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * @param T $element Value to remove
     *
     * @return bool True if removed, false if not found
     */
    public function remove($element): bool {
        $node = $this->findNode($element);
        if ($node === null) {
            return false;
        }
        $this->removeNode($node);

        return true;
    }

    /**
     * @param T $element Value to remove
     *
     * @return bool True if any removed, false if not found
     */
    public function removeAll($element): bool {
        $node = $this->findNode($element);
        $removed = false;
        for (; $node !== null && $node->value === $element;) {
            $old = $node;
            $node = $node->next;
            $this->removeNode($old);
            $removed = true;
        }

        return $removed;
    }

    /**
     * Checks if the list contains the specified element.
     *
     * @param T $element The element to check
     */
    public function has($element): bool {
        return $this->findNode($element) !== null;
    }

    /**
     * Counts the occurrences of the specified element in the list.
     *
     * @param T $element The element to count
     */
    public function countElements($element): int {
        $node = $this->findNode($element);
        $count = 0;
        for (; $node !== null && $node->value === $element; $node = $node->next) {
            ++$count;
        }

        return $count;
    }

    /**
     * Create a node for the given value, checking type.
     *
     * @param T $value Value to wrap
     *
     * @return SortedLinkedListNode<T> Node containing value
     *
     * @throws InvalidArgumentException If type does not match
     */
    private function createNode($value): SortedLinkedListNode {
        if ($this->type !== gettype($value)) {
            throw new InvalidArgumentException("Invalid type '{$this->type}'");
        }

        return new SortedLinkedListNode($value);
    }

    /**
     * Get the node at the given index.
     *
     * @param int $offset Index to retrieve
     *
     * @return null|SortedLinkedListNode<T> Node at index or null if out of bounds
     */
    private function getNode(int $offset): ?SortedLinkedListNode {
        $node = $this->head;
        for ($idx = 0; $node !== null && $idx < $offset; ++$idx) {
            $node = $node->next;
        }

        return $node;
    }

    /**
     * Get the node for the given element.
     *
     * @param T $element
     *
     * @return null|SortedLinkedListNode<T> Node at index or null if out of bounds
     */
    private function findNode($element): ?SortedLinkedListNode {
        for ($node = $this->head; $node !== null && $node->value < $element;) {
            $node = $node->next;
        }
        if ($node?->value === $element) {
            return $node;
        }

        return null;
    }

    /**
     * Remove a node from the list and update head/tail as needed.
     *
     * @param SortedLinkedListNode<T> $node Node to remove
     */
    private function removeNode(SortedLinkedListNode $node): void {
        if (!$node->hasPrev()) {
            $this->head = $node->next;
        }
        if (!$node->hasNext()) {
            $this->tail = $node->prev;
        }
        $node->detach();
        unset($node);
        if ($this->count > 0) {
            --$this->count;
        }
    }
}
