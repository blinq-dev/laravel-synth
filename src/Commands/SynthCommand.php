<?php

namespace Blinq\Synth\Commands;

use Blinq\Synth\Functions;
use Blinq\Synth\MainMenu;
use Blinq\Synth\Modules;
use Blinq\Synth\Synth;
use Illuminate\Console\Command;

/**
 * This file contains the main command for the Synth application.
 * It handles the execution of the command and manages the Synth, MainMenu, and Modules instances.
 */
class SynthCommand extends Command
{
    public $signature = 'synth';

    public $description = 'My command';

    public Synth $synth;

    public MainMenu $mainMenu;

    public Modules $modules;

    public function handle(): int
    {
        Functions::registerAll();

        $this->synth = new Synth($this);
        $this->synth->handleExitSignal();
        $this->synth->handleStream();

        $this->mainMenu = new MainMenu($this);
        $this->mainMenu->welcome();
        $this->modules = new Modules($this);

        return $this->mainMenu->handle();
    }
}
