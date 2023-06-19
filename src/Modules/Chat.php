<?php

namespace Blinq\Synth\Modules;

class Chat extends Module
{
    public $attachments = [];

    public function name() : string { 
        return "Chat";
    }

    public function register(): array { 
        return [
            'chat' => 'Chat with GPT',
            'make' => 'Create or update any file by asking',
        ];
    }

    public function onSelect(?string $key = null) {
        $forceSaveFiles = $key === "make";

        $this->cmd->synth->loadSystemMessage("chat");
        $schema = include __DIR__ . "/../Prompts/chat.schema.php";
        $currentQuestion = $key === "make" ? "What should I make?" : "How can I help you?";

        while(true) {
            $answer = $this->cmd->ask($currentQuestion);

            if ($answer == "exit" || !$answer) {
                break;
            }

            $this->cmd->synth->chat($answer, [
                ...$schema,
                ...($forceSaveFiles ? [
                    'function_call' => ['name' => 'save_files'],
                ]: [])
            ]);
            $this->cmd->synth->handleFunctionsForLastMessage();

            $this->cmd->newLine(2);
            $this->cmd->comment("Type something to refine, press enter to save and continue, type 'exit' to discard");
            $currentQuestion = "You";
        }
    }

}
