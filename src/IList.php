<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\LinkedList;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * @template T
 *
 * @extends IteratorAggregate<int,T>
 * @extends ArrayAccess<int,T>
 */
interface IList extends IteratorAggregate, ArrayAccess, Countable {
    /**
     * @param T $element
     */
    public function add($element): void;

    /**
     * @param T ...$elements
     */
    public function addAll(...$elements): void;
}
