<?php

namespace Blinq\Synth\Modules;

class Migrations extends Module
{
    public $attachments = [];

    public function name(): string
    {
        return 'Migrations';
    }

    public function register(): array
    {
        return [
            'migrations' => 'Generate migrations for your application.',
        ];
    }

    public function onSelect(?string $key = null)
    {
        $this->cmd->synth->loadSystemMessage('migrations');

        $schema = include __DIR__.'/../Prompts/migrations.schema.php';

        $architecture = $this->cmd->attachments['architecture'] ?? '';

        if ($architecture == '') {
            $this->cmd->error('You need to create an architecture first');

            return;
        }

        while (true) {
            $this->cmd->synth->chat('Please make migration(s)', [
                'temperature' => 0,
                'function_call' => ['name' => 'save_migrations'],
                ...$schema,
            ]);

            $this->cmd->newLine();
            $this->cmd->info("Type something to refine, press enter to save and continue, type 'exit' to discard");
            $answer = $this->cmd->ask('You');

            if ($answer == 'exit') {
                break;
            }

            if (! $answer) {
                $this->cmd->synth->handleFunctionsForLastMessage();

                break;
            }
        }
    }
}
