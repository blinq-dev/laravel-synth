<?php

namespace Blinq\Synth;

use Blinq\LLM\Entities\ChatMessage;
use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Traits\WithHooks;
use Illuminate\Console\Command;

class MainMenu
{
    use WithHooks;

    public $modules = [];

    public function __construct(public SynthCommand $cmd)
    {
        $this->on('show', function () {
            $this->showTokenCount();
        }, 100);
    }

    public function showTokenCount()
    {
        $tokens = $this->cmd->synth->estimateTokenCount();
        $history = $this->cmd->synth->ai->getHistory();

        if ($tokens > 0) {
            $this->cmd->info("Estimated token count: " . $tokens);
            $this->cmd->info("Number of messages: " . count($history));
            $this->cmd->newLine();
        }
    }

    public function welcome()
    {
        $this->cmd->info('--------------------------------------------');
        $this->cmd->info('░░░░░░░ ░░    ░░ ░░░    ░░ ░░░░░░░░ ░░   ░░ ');
        $this->cmd->info('▒▒       ▒▒  ▒▒  ▒▒▒▒   ▒▒    ▒▒    ▒▒   ▒▒ ');
        $this->cmd->info('▒▒▒▒▒▒▒   ▒▒▒▒   ▒▒ ▒▒  ▒▒    ▒▒    ▒▒▒▒▒▒▒ ');
        $this->cmd->info('     ▓▓    ▓▓    ▓▓  ▓▓ ▓▓    ▓▓    ▓▓   ▓▓ ');
        $this->cmd->info('███████    ██    ██   ████    ██    ██   ██ ');
        $this->cmd->info('--------------------------------------------');
    }

    public function handle()
    {
        $moduleOptions = $this->cmd->modules->getOptions();

        $options = [
            ...$moduleOptions,
            'exit' => 'Exit',
        ];

        while (true) {
            $this->dispatch('show');

            $option = $this->cmd->choice('What do you want to do?', $options);

            $this->cmd->modules->select($option);

            if ($option === 'exit') {
                return Command::SUCCESS;
            }
        }
    }
}
