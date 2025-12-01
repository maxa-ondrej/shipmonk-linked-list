<?php

declare(strict_types=1);

namespace MaxaOndrej\ShipMonk\Collections\LinkedList;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class SortedLinkedListTest extends TestCase {
    public function testStringTypeEmpty(): void {
        $list = SortedLinkedList::string();
        $this->assertSame(0, $list->count());
        $this->assertSame([], [...$list->getIterator()]);
    }

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

    public function testStringTypeRemoveValues(): void {
        $list = SortedLinkedList::string('text 3', 'ahoj', 'text 2', 'text 1');
        $list->remove('text 2');
        $this->assertSame(['ahoj', 'text 1', 'text 3'], [...$list->getIterator()]);
    }

    public function testIntTypeEmpty(): void {
        $list = SortedLinkedList::int();
        $this->assertSame(0, $list->count());
        $this->assertSame([], [...$list->getIterator()]);
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

    public function testIntTypeRemoveValues(): void {
        $list = SortedLinkedList::int(3, -100, 99, 0);
        $list->remove(99);
        $this->assertSame([-100, 0, 3], [...$list->getIterator()]);
    }

    public function testRemoveNonExistentElement(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $result = $list->remove(5);
        $this->assertFalse($result);
        $this->assertSame(3, $list->count());
    }

    public function testRemoveFromEmptyList(): void {
        $list = SortedLinkedList::string();
        $result = $list->remove('test');
        $this->assertFalse($result);
        $this->assertSame(0, $list->count());
    }

    public function testRemoveFirstElement(): void {
        $list = SortedLinkedList::string('a', 'b', 'c');
        $list->remove('a');
        $this->assertSame(['b', 'c'], [...$list->getIterator()]);
        $this->assertSame(2, $list->count());
    }

    public function testRemoveLastElement(): void {
        $list = SortedLinkedList::string('a', 'b', 'c');
        $list->remove('c');
        $this->assertSame(['a', 'b'], [...$list->getIterator()]);
        $this->assertSame(2, $list->count());
    }

    public function testRemoveMiddleElement(): void {
        $list = SortedLinkedList::string('a', 'b', 'c');
        $list->remove('b');
        $this->assertSame(['a', 'c'], [...$list->getIterator()]);
        $this->assertSame(2, $list->count());
    }

    public function testRemoveAllWithDuplicates(): void {
        $list = SortedLinkedList::int(1, 2, 2, 2, 3, 4);
        $result = $list->removeAll(2);
        $this->assertTrue($result);
        $this->assertSame([1, 3, 4], [...$list->getIterator()]);
        $this->assertSame(3, $list->count());
    }

    public function testRemoveAllNoDuplicates(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $result = $list->removeAll(2);
        $this->assertTrue($result);
        $this->assertSame([1, 3], [...$list->getIterator()]);
        $this->assertSame(2, $list->count());
    }

    public function testRemoveAllNonExistent(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $result = $list->removeAll(5);
        $this->assertFalse($result);
        $this->assertSame([1, 2, 3], [...$list->getIterator()]);
        $this->assertSame(3, $list->count());
    }

    public function testRemoveAllFromEmptyList(): void {
        $list = SortedLinkedList::string();
        $result = $list->removeAll('test');
        $this->assertFalse($result);
        $this->assertSame(0, $list->count());
    }

    public function testRemoveAllFirstElements(): void {
        $list = SortedLinkedList::string('a', 'a', 'a', 'b', 'c');
        $result = $list->removeAll('a');
        $this->assertTrue($result);
        $this->assertSame(['b', 'c'], [...$list->getIterator()]);
    }

    public function testRemoveAllLastElements(): void {
        $list = SortedLinkedList::string('a', 'b', 'c', 'c', 'c');
        $result = $list->removeAll('c');
        $this->assertTrue($result);
        $this->assertSame(['a', 'b'], [...$list->getIterator()]);
    }

    public function testRemoveAllEntireList(): void {
        $list = SortedLinkedList::int(5, 5, 5, 5);
        $result = $list->removeAll(5);
        $this->assertTrue($result);
        $this->assertSame([], [...$list->getIterator()]);
        $this->assertSame(0, $list->count());
    }

    public function testGetIteratorReversedEmpty(): void {
        $list = SortedLinkedList::int();
        $this->assertSame([], [...$list->getIteratorReversed()]);
    }

    public function testGetIteratorReversedInt(): void {
        $list = SortedLinkedList::int(1, 5, 3, 2, 4);
        $this->assertSame([5, 4, 3, 2, 1], [...$list->getIteratorReversed()]);
    }

    public function testGetIteratorReversedString(): void {
        $list = SortedLinkedList::string('zebra', 'apple', 'mango', 'banana');
        $this->assertSame(['zebra', 'mango', 'banana', 'apple'], [...$list->getIteratorReversed()]);
    }

    public function testGetIteratorReversedSingleElement(): void {
        $list = SortedLinkedList::int(42);
        $this->assertSame([42], [...$list->getIteratorReversed()]);
    }

    public function testOffsetExistsTrue(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $this->assertTrue(isset($list[0]));
        $this->assertTrue(isset($list[1]));
        $this->assertTrue(isset($list[2]));
    }

    public function testOffsetExistsFalse(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $this->assertFalse(isset($list[3]));
        $this->assertFalse(isset($list[10]));
    }

    public function testOffsetGet(): void {
        $list = SortedLinkedList::int(10, 20, 30);
        $this->assertSame(10, $list[0]);
        $this->assertSame(20, $list[1]);
        $this->assertSame(30, $list[2]);
    }

    public function testOffsetGetOutOfBounds(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $this->assertNull($list[10]);
    }

    public function testOffsetGetStringList(): void {
        $list = SortedLinkedList::string('zebra', 'apple', 'banana');
        $this->assertSame('apple', $list[0]);
        $this->assertSame('banana', $list[1]);
        $this->assertSame('zebra', $list[2]);
    }

    public function testOffsetSetThrowsException(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This list type does not allow modification at a specific index.');
        $list[0] = 99;
    }

    public function testOffsetUnset(): void {
        $list = SortedLinkedList::int(1, 2, 3, 4, 5);
        unset($list[2]);
        $this->assertSame([1, 2, 4, 5], [...$list->getIterator()]);
        $this->assertSame(4, $list->count());
    }

    public function testOffsetUnsetFirst(): void {
        $list = SortedLinkedList::string('a', 'b', 'c');
        unset($list[0]);
        $this->assertSame(['b', 'c'], [...$list->getIterator()]);
    }

    public function testOffsetUnsetLast(): void {
        $list = SortedLinkedList::string('a', 'b', 'c');
        unset($list[2]);
        $this->assertSame(['a', 'b'], [...$list->getIterator()]);
    }

    public function testOffsetUnsetOutOfBounds(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        unset($list[10]);
        $this->assertSame([1, 2, 3], [...$list->getIterator()]);
        $this->assertSame(3, $list->count());
    }

    public function testHeadProperty(): void {
        $list = SortedLinkedList::int(5, 1, 3);
        $this->assertNotNull($list->head);
        $this->assertSame(1, $list->head->value);
    }

    public function testHeadPropertyEmptyList(): void {
        $list = SortedLinkedList::int();
        $this->assertNull($list->head);
    }

    public function testTailProperty(): void {
        $list = SortedLinkedList::int(5, 1, 3);
        $this->assertNotNull($list->tail);
        $this->assertSame(5, $list->tail->value);
    }

    public function testTailPropertyEmptyList(): void {
        $list = SortedLinkedList::int();
        $this->assertNull($list->tail);
    }

    public function testHeadAndTailSingleElement(): void {
        $list = SortedLinkedList::string('solo');
        $this->assertNotNull($list->head);
        $this->assertNotNull($list->tail);
        $this->assertSame('solo', $list->head->value);
        $this->assertSame('solo', $list->tail->value);
        $this->assertSame($list->head, $list->tail);
    }

    public function testAddAllIntegers(): void {
        $list = SortedLinkedList::int();
        $list->addAll(5, 2, 8, 1, 9);
        $this->assertSame([1, 2, 5, 8, 9], [...$list->getIterator()]);
        $this->assertSame(5, $list->count());
    }

    public function testAddAllStrings(): void {
        $list = SortedLinkedList::string();
        $list->addAll('delta', 'alpha', 'charlie', 'bravo');
        $this->assertSame(['alpha', 'bravo', 'charlie', 'delta'], [...$list->getIterator()]);
    }

    public function testAddDuplicates(): void {
        $list = SortedLinkedList::int(1, 2, 3);
        $list->add(2);
        $list->add(2);
        $this->assertSame([1, 2, 2, 2, 3], [...$list->getIterator()]);
        $this->assertSame(5, $list->count());
    }

    public function testStringsSortedAlphabetically(): void {
        $list = SortedLinkedList::string('zoo', 'apple', 'Zebra', 'banana');
        // Note: strcmp is case-sensitive, uppercase comes before lowercase
        $this->assertSame(['Zebra', 'apple', 'banana', 'zoo'], [...$list->getIterator()]);
    }

    public function testNegativeNumbers(): void {
        $list = SortedLinkedList::int(-5, -1, -10, 0, 5);
        $this->assertSame([-10, -5, -1, 0, 5], [...$list->getIterator()]);
    }

    public function testLargeNumberOfElements(): void {
        $list = SortedLinkedList::int();
        for ($i = 100; $i >= 1; --$i) {
            $list->add($i);
        }
        $this->assertSame(100, $list->count());
        $this->assertSame(1, $list[0]);
        $this->assertSame(100, $list[99]);
    }

    public function testRemoveAndReAdd(): void {
        $list = SortedLinkedList::int(1, 2, 3, 4, 5);
        $list->remove(3);
        $this->assertSame([1, 2, 4, 5], [...$list->getIterator()]);
        $list->add(3);
        $this->assertSame([1, 2, 3, 4, 5], [...$list->getIterator()]);
    }

    public function testCountAfterOperations(): void {
        $list = SortedLinkedList::string();
        $this->assertSame(0, $list->count());

        $list->add('a');
        $this->assertSame(1, $list->count());

        $list->addAll('b', 'c', 'd');
        $this->assertSame(4, $list->count());

        $list->remove('b');
        $this->assertSame(3, $list->count());

        $list->removeAll('z');
        $this->assertSame(3, $list->count());
    }

    public function testEmptyStringValues(): void {
        $list = SortedLinkedList::string('', 'a', '', 'b');
        $this->assertSame(['', '', 'a', 'b'], [...$list->getIterator()]);
    }

    public function testZeroValues(): void {
        $list = SortedLinkedList::int(0, -1, 1, 0);
        $this->assertSame([-1, 0, 0, 1], [...$list->getIterator()]);
    }

    public function testHasElementInt(): void {
        $list = SortedLinkedList::int(1, 2, 2, 3, 4);
        $this->assertTrue($list->has(2));
        $this->assertTrue($list->has(1));
        $this->assertTrue($list->has(4));
        $this->assertFalse($list->has(99));
        $this->assertFalse($list->has(-100));
    }

    public function testHasElementString(): void {
        $list = SortedLinkedList::string('apple', 'banana', 'banana', 'cherry');
        $this->assertTrue($list->has('banana'));
        $this->assertTrue($list->has('apple'));
        $this->assertTrue($list->has('cherry'));
        $this->assertFalse($list->has('pear'));
        $this->assertFalse($list->has(''));
    }

    public function testCountElementsInt(): void {
        $list = SortedLinkedList::int(1, 2, 2, 2, 3, 4);
        $this->assertSame(3, $list->countElements(2));
        $this->assertSame(1, $list->countElements(1));
        $this->assertSame(1, $list->countElements(3));
        $this->assertSame(0, $list->countElements(99));
    }

    public function testCountElementsString(): void {
        $list = SortedLinkedList::string('apple', 'banana', 'banana', 'cherry', 'banana');
        $this->assertSame(3, $list->countElements('banana'));
        $this->assertSame(1, $list->countElements('apple'));
        $this->assertSame(1, $list->countElements('cherry'));
        $this->assertSame(0, $list->countElements('pear'));
    }

    public function testHasAndCountElementsEmptyList(): void {
        $list = SortedLinkedList::int();
        $this->assertFalse($list->has(1));
        $this->assertSame(0, $list->countElements(1));
        $listStr = SortedLinkedList::string();
        $this->assertFalse($listStr->has('a'));
        $this->assertSame(0, $listStr->countElements('a'));
    }
}
