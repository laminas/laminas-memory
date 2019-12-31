<?php

/**
 * @see       https://github.com/laminas/laminas-memory for the canonical source repository
 * @copyright https://github.com/laminas/laminas-memory/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-memory/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Memory;

use Laminas\Memory;
use Laminas\Memory\Container;

/**
 * @group      Laminas_Memory
 */
class MovableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertInstanceOf(Container\Movable::class, $memObject);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string) $memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string) $memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertInstanceOf(Memory\Value::class, $memObject->value);
        $this->assertEquals((string) $memObject->value, 'another value');
    }

    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertFalse($memObject->isLocked());

        $memObject->lock();
        $this->assertTrue($memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse($memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->touch();

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertEquals($memObject, $memoryManager->processedObject);
        $this->assertEquals(10, $memoryManager->processedId);
    }

    /**
     * tests the value update tracing
     */
    public function testValueUpdateTracing()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        // startTrace() method is usually invoked by memory manager, when it need to be notified
        // about value update
        $memObject->startTrace();

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->value[6] = '_';

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertEquals($memObject, $memoryManager->processedObject);
        $this->assertEquals(10, $memoryManager->processedId);
    }

    public function testInvalidGetThrowException()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');
        $this->setExpectedException(Memory\Exception\InvalidArgumentException::class);
        $value = $memObject->unknowProperty;
    }

    public function testInvalidSetThrowException()
    {
        $memoryManager = new TestAsset\DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');
        $this->setExpectedException(Memory\Exception\InvalidArgumentException::class);
        $memObject->unknowProperty = 5;
    }
}
