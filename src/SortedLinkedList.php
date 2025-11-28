<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\LinkedList;

use InvalidArgumentException;
use RuntimeException;
use Traversable;

use function gettype;

/**
 * @template T of int|string
 *
 * @implements IList<T>
 */
class SortedLinkedList implements IList {
    private function __construct(private readonly string $type) {}

    /**
     * @param int ...$values
     *
     * @return SortedLinkedList<int>
     */
    public static function int(...$values): self {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @param string ...$values
     *
     * @return SortedLinkedList<string>
     */
    public static function string(...$values): self {
        throw new RuntimeException('Not implemented');
    }

    public function getIterator(): Traversable {
        throw new RuntimeException('Not implemented');
    }

    public function offsetExists(mixed $offset): bool {
        throw new RuntimeException('Not implemented');
    }

    public function offsetGet(mixed $offset): mixed {
        throw new RuntimeException('Not implemented');
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        throw new InvalidArgumentException('This list type does not allow modification at a specific index.');
    }

    public function offsetUnset(mixed $offset): void {
        throw new RuntimeException('Not implemented');
    }

    public function count(): int {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @param T $element
     */
    public function add($element): void {
        $this->checkType($element);

        // TODO: implement
    }

    /**
     * @param T ...$elements
     */
    public function addAll(...$elements): void {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Additional check to make sure value has the correct type.
     *
     * @param T $value
     */
    private function checkType($value): void {
        if ($this->type !== gettype($value)) {
            throw new InvalidArgumentException("Invalid type '{$this->type}'");
        }
    }
}
