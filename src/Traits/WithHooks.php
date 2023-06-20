<?php

namespace Blinq\Synth\Traits;

trait WithHooks
{
    public $hooks = [];

    public function on($event, $callback, $prio = 0)
    {
        $this->hooks[$event][$prio][] = $callback;
    }

    public function dispatch($event, $args = [])
    {
        if (!isset($this->hooks[$event])) {
            return;
        }

        $hooks = $this->hooks[$event];

        ksort($hooks);

        foreach ($hooks as $prio => $callbacks) {
            foreach ($callbacks as $callback) {
                $callback(...$args);
            }
        }
    }
}
