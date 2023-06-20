<?php

namespace Blinq\Synth\Modules;

/**
 * This file is a module in the Synth application, specifically for handling the generation of migrations.
 * It provides functionality to register, select, and refine migrations based on the architecture.
 */
class Migrations extends Module
{
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

        if (! $this->cmd->modules->get('Attachments')->getAttachments('architecture')) {
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
            $this->cmd->info("Press enter to accept and continue, type 'exit' to discard, or ask a follow up question.");
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
