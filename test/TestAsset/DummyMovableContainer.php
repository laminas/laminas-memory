<?php

namespace LaminasTest\Memory\TestAsset;

use Laminas\Memory\Container\Movable;

class DummyMovableContainer extends Movable
{
    /**
     * Empty constructor
     */
    public function __construct()
    {
        // Do nothing
    }

    /**
     * Dummy value update callback method
     */
    public function processUpdate()
    {
        // Do nothing
    }
}
