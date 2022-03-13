<?php

declare(strict_types=1);

namespace Laminas\Memory\Container;

/**
 * Memory value container interface
 */
interface ContainerInterface
{
    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return &string
     */
    public function &getRef();

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch();

    /**
     * Lock object in memory.
     */
    public function lock();

    /**
     * Unlock object
     */
    public function unlock();

    /**
     * Return true if object is locked
     *
     * @return bool
     */
    public function isLocked();
}
