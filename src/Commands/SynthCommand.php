<?php

namespace Blinq\Synth\Commands;

use Blinq\Synth\Functions;
use Blinq\Synth\MainMenu;
use Blinq\Synth\Modules;
use Blinq\Synth\Synth;
use Illuminate\Console\Command;

class SynthCommand extends Command
{
    public $signature = 'synth';

    public $description = 'My command';

    public Synth $synth;

    public MainMenu $mainMenu;

    public Modules $modules;

    public function handle(): int
    {
        Functions::registrerAll();

        $this->synth = new Synth($this);
        $this->synth->handleExitSignal();
        $this->synth->handleStream();

        $this->mainMenu = new MainMenu($this);
        $this->mainMenu->welcome();
        $this->modules = new Modules($this);

        return $this->mainMenu->handle();
    }
}
