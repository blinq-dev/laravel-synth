<?php

namespace Blinq\Synth;

use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Modules\Module;

class Modules
{
    protected static $modules = [];

    public function __construct(public SynthCommand $cmd)
    {
        static::register(new Modules\Attachments($cmd));
        static::register(new Modules\Chat($cmd));
        static::register(new Modules\Architect($cmd));
        static::register(new Modules\Migrations($cmd));
        static::register(new Modules\Models($cmd));
    }

    public static function register(Module $module)
    {
        self::$modules[] = [
            'name' => $module->name(),
            'module' => $module,
            'options' => $module->register(),
        ];
    }

    public function get($name)
    {
        return collect(self::$modules)
            ->firstWhere('name', $name)['module'] ?? null;
    }

    public function getOptions() : array
    {
        return collect(self::$modules)->flatMap(function($x) {
            return $x['options'];
        })->toArray();
    }

    public function select(?string $option = null)
    {
        foreach(self::$modules as $module) {
            if ($module['options'][$option] ?? null) {
                $module['module']->onSelect($option);
            }
        }
    }
}
