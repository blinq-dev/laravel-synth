<?php

namespace Blinq\Synth\Traits;

trait WithHooks
{
    public $hooks = [];

    public function on($event, $callback)
    {
        $this->hooks[$event][] = $callback;
    }

    public function dispatch($event, $args = [])
    {
        if ($this->hooks[$event] ?? false) {
            foreach ($this->hooks[$event] as $callback) {
                $callback(...$args);
            }
        }
    }
}
