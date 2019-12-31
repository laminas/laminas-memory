<?php

/**
 * @see       https://github.com/laminas/laminas-memory for the canonical source repository
 * @copyright https://github.com/laminas/laminas-memory/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-memory/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Memory;

use Laminas\Memory\Container;

/**
 * @category   Laminas
 * @package    Laminas_Memory
 * @subpackage UnitTests
 * @group      Laminas_Memory
 */
class LockedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memObject = new Container\Locked('0123456789');

        $this->assertTrue($memObject instanceof Container\Locked);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memObject = new Container\Locked('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertEquals((string)$memObject->value, 'another value');
    }

    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memObject = new Container\Locked('0123456789');

        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memObject = new Container\Locked('0123456789');

        $memObject->touch();

        // Nothing to check
    }
}
