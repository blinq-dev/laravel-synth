<?php

namespace Blinq\Synth\Modules;

use Blinq\LLM\Entities\ChatMessage;

class Architect extends Module
{
    public $attachments = [];

    public function name() : string { 
        return "Architect";
    }

    public function register(): array { 
        return [
            'architect' => 'Brainstorm with GPT to generate a new application architecture.',
        ];
    }

    public function onSelect(?string $key = null) {
        $this->cmd->synth->loadSystemMessage("architect");
        // $schema = include __DIR__ . "/../Prompts/architect.schema.php";
        $currentQuestion = "What do you want to create?";
        $hasAnswered = false;

        while(true) {
            $input = $this->cmd->ask($currentQuestion);

            if ($input == "exit") {
                break;
            }

            if (!$input) {
                if ($hasAnswered) {
                    $this->cmd->modules->get("Attachments")->addAttachmentFromMessage("architecture", $this->cmd->synth->ai->getLastMessage());
                }
      
                break;
            }

            $this->cmd->synth->chat($input);
            $hasAnswered = true;

            $this->cmd->newLine();
            $this->cmd->info("Type something to refine, press enter to save and continue, type 'exit' to discard");
            $currentQuestion = "You";
        }
    }

}
