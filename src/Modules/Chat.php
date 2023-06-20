<?php

namespace Blinq\Synth\Modules;

/**
 * This file is a module in the Synth application, specifically for handling chat interactions.
 * It provides functionality to chat with GPT and create/update files using the chat interface.
 */
class Chat extends Module
{
    public $attachments = [];

    public function name(): string
    {
        return 'Chat';
    }

    public function register(): array
    {
        return [
            'chat' => 'Chat with GPT',
            'make' => 'Create or update any file by asking',
        ];
    }

    public function onSelect(?string $key = null)
    {
        $forceSaveFiles = $key === 'make';

        $this->cmd->synth->loadSystemMessage('chat');
        $schema = include __DIR__.'/../Prompts/chat.schema.php';
        $currentQuestion = $key === 'make' ? 'What should I make?' : 'How can I help you?';

        while (true) {
            $answer = $this->cmd->ask($currentQuestion);

            if ($answer == 'exit' || ! $answer) {
                break;
            }

            $this->cmd->synth->chat($answer, [
                ...$schema,
                ...($forceSaveFiles ? [
                    'function_call' => ['name' => 'save_files'],
                ] : []),
            ]);
            $this->cmd->synth->handleFunctionsForLastMessage();

            $this->cmd->newLine(2);
            $this->cmd->comment("Press enter to accept and continue, type 'exit' to discard, or ask a follow up question.");
            $currentQuestion = 'You';
        }
    }
}
