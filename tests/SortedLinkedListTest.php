<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\LinkedList;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class SortedLinkedListTest extends TestCase {
    public function testStringType(): void {
        $list = SortedLinkedList::string();
        $list->add('text 3');
        $list->add('text 1');
        $list->add('ahoj');
        $list->add('text 2');
        $this->assertSame(4, $list->count());
        $this->assertSame(['ahoj', 'text 1', 'text 2', 'text 3'], [...$list->getIterator()]);
    }

    public function testStringTypeDisallowOtherTypes(): void {
        $list = SortedLinkedList::string();
        $this->expectException(InvalidArgumentException::class);
        $list->add(5);
        $list->add(true);
        $list->add([]);
        $list->add(new stdClass());
    }

    public function testStringTypeProvideValues(): void {
        $list = SortedLinkedList::string('text 3', 'ahoj', 'text 2');
        $list->add('text 1');
        $this->assertSame(['ahoj', 'text 1', 'text 2', 'text 3'], [...$list->getIterator()]);
    }

    public function testIntType(): void {
        $list = SortedLinkedList::int();
        $list->add(3);
        $list->add(0);
        $list->add(-100);
        $list->add(99);
        $this->assertSame(4, $list->count());
        $this->assertSame([-100, 0, 3, 99], [...$list->getIterator()]);
    }

    public function testIntTypeDisallowOtherTypes(): void {
        $list = SortedLinkedList::int();
        $this->expectException(InvalidArgumentException::class);
        $list->add('ahoj');
        $list->add(3.5);
        $list->add(true);
        $list->add([]);
        $list->add(new stdClass());
    }

    public function testIntTypeProvideValues(): void {
        $list = SortedLinkedList::int(3, -100, 99);
        $list->add(0);
        $this->assertSame([-100, 0, 3, 99], [...$list->getIterator()]);
    }
}
