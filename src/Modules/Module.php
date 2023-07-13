<?php

namespace Blinq\Synth\Modules;

use Blinq\Synth\Commands\SynthCommand;

/**
 * This file defines the base class for modules in the Synth application.
 * It provides common functionality for modules, such as registering, selecting, and accessing other modules.
 */
abstract class Module
{
    public function __construct(public SynthCommand $cmd)
    {

    }

    abstract public function name(): string;

    public function register(): array
    {
        return [];
    }

    public function onSelect(string $key = null)
    {

    }

    public function getModule($name)
    {
        return $this->cmd->modules->get($name);
    }
}
