<?php

namespace Blinq\Synth;

class Functions
{
    protected static $functions = [];

    public static function register($key, $callback)
    {
        if (self::$functions[$key] ?? false) {
            throw new \Exception("Function $key already registered");
        }

        self::$functions[$key] = $callback;
    }

    public static function call($key, ...$args)
    {
        return self::$functions[$key](...$args);
    }

    public static function registrerAll() {
        foreach(glob(__DIR__ . '/Functions/*.php') as $file) {
            require_once $file;
        }
    }
}
