<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections;

use Iterator;

/**
 * Iterator for traversing a list.
 *
 * Provides iteration over the elements of a list.
 *
 * @template T
 *
 * @extends Iterator<int,T>
 */
interface IListIterator extends Iterator {}
