<?php

/**
 * @see       https://github.com/laminas/laminas-memory for the canonical source repository
 * @copyright https://github.com/laminas/laminas-memory/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-memory/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Memory\Container;

/**
 * Memory value container
 *
 * Locked (always stored in memory).
 *
 * @category   Laminas
 * @package    Laminas_Memory
 */
class Locked extends AbstractContainer
{
    /**
     * Value object
     *
     * @var string
     */
    public $value;


    /**
     * Object constructor
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Lock object in memory.
     */
    public function lock()
    {
        /* Do nothing */
    }

    /**
     * Unlock object
     */
    public function unlock()
    {
        /* Do nothing */
    }

    /**
     * Return true if object is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return true;
    }

    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return &string
     */
    public function &getRef()
    {
        return $this->value;
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        /* Do nothing */
    }

    /**
     * Destroy memory container and remove it from memory manager list
     */
    public function destroy()
    {
        /* Do nothing */
    }
}
