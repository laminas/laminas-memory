<?php

declare(strict_types=1);

namespace Laminas\Memory;

use ArrayAccess;
use Countable;
use ReturnTypeWillChange;

use function strlen;

/**
 * String value object
 *
 * It's an OO string wrapper.
 * Used to intercept string updates.
 */
class Value implements ArrayAccess, Countable
{
    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Container
     *
     * @var Movable
     */
    private $container;

    /**
     * Boolean flag which signals to trace value modifications
     *
     * @var bool
     */
    private $trace;

    /**
     * Object constructor
     *
     * @param string $value
     */
    public function __construct($value, Container\Movable $container)
    {
        $this->container = $container;

        $this->value = (string) $value;

        /**
         * Object is marked as just modified by memory manager
         * So we don't need to trace followed object modifications and
         * object is processed (and marked as traced) when another
         * memory object is modified.
         *
         * It reduces overall number of calls necessary to modification trace
         */
        $this->trace = false;
    }

    /**
     * Countable
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return strlen($this->value);
    }

    /**
     * ArrayAccess interface method
     * returns true if string offset exists
     *
     * @param int $offset
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < strlen($this->value);
    }

    /**
     * ArrayAccess interface method
     * Get character at $offset position
     *
     * @param int $offset
     * @return string
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->value[$offset];
    }

    /**
     * ArrayAccess interface method
     * Set character at $offset position
     *
     * @param int $offset
     * @param string $char
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $char)
    {
        $this->value[$offset] = $char;

        if ($this->trace) {
            $this->trace = false;
            $this->container->processUpdate();
        }
    }

    /**
     * ArrayAccess interface method
     * Unset character at $offset position
     *
     * @param int $offset
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);

        if ($this->trace) {
            $this->trace = false;
            $this->container->processUpdate();
        }
    }

    /**
     * To string conversion
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @internal
     *
     * @return string
     */
    public function &getRef()
    {
        return $this->value;
    }

    /**
     * Start modifications trace
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @internal
     */
    public function startTrace()
    {
        $this->trace = true;
    }
}
