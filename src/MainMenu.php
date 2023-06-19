<?php

namespace Blinq\Synth;

use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Traits\WithHooks;
use Illuminate\Console\Command;

class MainMenu
{
    use WithHooks;

    public $modules = [];

    public function __construct(public SynthCommand $cmd)
    {
        
    }

    public function welcome()
    {
        // Let's find out what the user wants
        $this->cmd->info("-----------------------------------------------");
        $this->cmd->info('Laravel Synth');
        $this->cmd->info("-----------------------------------------------");
    }

    public function handle()
    {
        $moduleOptions = $this->cmd->modules->getOptions();

        $options = [
            ...$moduleOptions,
            'exit' => 'Exit',
        ];
        
        while(true) {
            $this->dispatch('show');

            $option = $this->cmd->choice('What do you want to do?', $options);

            $this->cmd->modules->select($option);

            if ($option === "exit") {
                return Command::SUCCESS;
            }
        }
    }
}
