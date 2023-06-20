<?php

namespace Blinq\Synth;

use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Modules\Module;

/**
 * This file is responsible for managing the modules in the Synth application.
 * It includes functionality to register, retrieve, and interact with modules.
 */
class Modules
{
    protected static $modules = [];
    protected static ?SynthCommand $cmd = null;

    public function __construct(SynthCommand $cmd)
    {
        static::$cmd = $cmd;
        static::register(Modules\Attachments::class);
        static::register(Modules\Chat::class);
        static::register(Modules\Architect::class);
        static::register(Modules\Migrations::class);
        static::register(Modules\Models::class);
        static::register(Modules\Files::class);
    }

    public static function register($module)
    {
        $module = new $module(static::$cmd);
        
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

    public function getOptions(): array
    {
        return collect(self::$modules)->flatMap(function ($x) {
            return $x['options'];
        })->toArray();
    }

    public function select(?string $option = null)
    {
        foreach (self::$modules as $module) {
            if ($module['options'][$option] ?? null) {
                $module['module']->onSelect($option);
            }
        }
    }
}
