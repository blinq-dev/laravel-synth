<?php

namespace Blinq\Synth\Modules;

use Blinq\LLM\Entities\ChatMessage;
use Blinq\Synth\Commands\SynthCommand;

abstract class Module
{
    public function __construct(public SynthCommand $cmd)
    {
        
    }

    abstract public function name() : string;

    public function register() : array {
        return [];
    }
    public function onSelect(?string $key = null) {

    }

}
