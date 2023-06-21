<?php

namespace Blinq\Synth\Modules;

use Blinq\LLM\Entities\ChatMessage;

/**
 * This file is a module in the Synth application, specifically for handling application architecture.
 * It provides functionality to brainstorm and generate a new application architecture using GPT.
 */
class History extends Module
{
    public function name(): string
    {
        return 'History';
    }

    public function register(): array
    {
        return [
            'history' => 'Show the chat history',
        ];
    }

    public function onSelect(?string $key = null)
    {
        $history = $this->cmd->synth->ai->getHistory();

        /**
         * @var ChatMessage $item
         */
        foreach ($history as $item) {
            $this->cmd->comment($item->role);
            $this->cmd->comment('----');
            $this->cmd->line($item->content ?? $item->function_call['name'] ?? '');
            $this->cmd->newLine();
            $this->cmd->newLine();
        }
    }
}
