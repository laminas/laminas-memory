<?php

declare(strict_types=1);

namespace Laminas\Memory\Container;

use Laminas\Memory;
use Laminas\Memory\Exception;

use function sprintf;

/**
 * Memory value container
 *
 * Movable (may be swapped with specified backend and unloaded).
 */
class Movable extends AbstractContainer
{
    /**
     * Internal object Id
     *
     * @var int
     */
    protected $id;

    /**
     * Memory manager reference
     *
     * @var Memory\MemoryManager
     */
    private $memManager;

    /**
     * Value object
     *
     * @var Memory\Value
     */
    private $value;

    /** Value states */
    public const LOADED  = 1;
    public const SWAPPED = 2;
    public const LOCKED  = 4;

    /**
     * Value state (LOADED/SWAPPED/LOCKED)
     *
     * @var int
     */
    private $state;

    /**
     * Object constructor
     *
     * @param int $id
     * @param string $value
     */
    public function __construct(Memory\MemoryManager $memoryManager, $id, $value)
    {
        $this->memManager = $memoryManager;
        $this->id         = $id;
        $this->state      = self::LOADED;
        $this->value      = new Memory\Value($value, $this);
    }

    /**
     * Lock object in memory.
     */
    public function lock()
    {
        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        if (! ($this->state & self::LOADED)) {
            $this->memManager->load($this, $this->id);
            $this->state |= self::LOADED;
        }

        $this->state |= self::LOCKED;

        /**
         * @todo
         * It's possible to set "value" container attribute to avoid modification tracing, while it's locked
         * Check, if it's  more effective
         */
    }

    /**
     * Unlock object
     */
    public function unlock()
    {
        // Clear LOCKED state bit
        $this->state &= ~self::LOCKED;
    }

    /**
     * Return true if object is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        return (bool) ($this->state & self::LOCKED);
    }

    /**
     * Get handler
     *
     * Loads object if necessary and moves it to the top of loaded objects list.
     * Swaps objects from the bottom of loaded objects list, if necessary.
     *
     * @param string $property
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function __get($property)
    {
        if ($property !== 'value') {
            throw new Exception\InvalidArgumentException(sprintf(
                'Unknown property: %s::$%s',
                self::class,
                $property
            ));
        }

        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        if (! ($this->state & self::LOADED)) {
            $this->memManager->load($this, $this->id);
            $this->state |= self::LOADED;
        }

        return $this->value;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  string $value
     * @throws Exception\InvalidArgumentException
     */
    public function __set($property, $value)
    {
        if ($property !== 'value') {
            throw new Exception\InvalidArgumentException(sprintf(
                'Unknown property: %s::$%s',
                self::class,
                $property
            ));
        }

        $this->state = self::LOADED;
        $this->value = new Memory\Value($value, $this);

        $this->memManager->processUpdate($this, $this->id);
    }

    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return string
     */
    public function &getRef()
    {
        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        if (! ($this->state & self::LOADED)) {
            $this->memManager->load($this, $this->id);
            $this->state |= self::LOADED;
        }

        return $this->value->getRef();
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        $this->memManager->processUpdate($this, $this->id);
    }

    /**
     * Process container value update.
     * Must be called only by value object
     *
     * @internal
     */
    public function processUpdate()
    {
        // Clear SWAPPED state bit
        $this->state &= ~self::SWAPPED;

        $this->memManager->processUpdate($this, $this->id);
    }

    /**
     * Start modifications trace
     *
     * @internal
     */
    public function startTrace()
    {
        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        if (! ($this->state & self::LOADED)) {
            $this->memManager->load($this, $this->id);
            $this->state |= self::LOADED;
        }

        $this->value->startTrace();
    }

    /**
     * Set value (used by memory manager when value is loaded)
     *
     * @internal
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = new Memory\Value($value, $this);
    }

    /**
     * Clear value (used by memory manager when value is swapped)
     *
     * @internal
     */
    public function unloadValue()
    {
        // Clear LOADED state bit
        $this->state &= ~self::LOADED;

        $this->value = null;
    }

    /**
     * Mark, that object is swapped
     *
     * @internal
     */
    public function markAsSwapped()
    {
        // Set SWAPPED state bit
        $this->state |= self::SWAPPED;
    }

    /**
     * Check if object is marked as swapped
     *
     * @internal
     *
     * @return bool
     */
    public function isSwapped()
    {
        // phpcs:ignore WebimpressCodingStandard.Formatting.Reference.UnexpectedSpace
        return $this->state & self::SWAPPED;
    }

    /**
     * Get object id
     *
     * @internal
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Destroy memory container and remove it from memory manager list
     *
     * @internal
     */
    public function destroy()
    {
        /**
         * We don't clean up swap because of performance considerations
         * Cleaning is performed by Memory Manager destructor
         */

        $this->memManager->unlink($this, $this->id);
    }
}
