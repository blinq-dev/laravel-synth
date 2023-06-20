<?php

namespace Blinq\Synth\Modules;

use Blinq\LLM\Entities\ChatMessage;

/**
 * This file is a module in the Synth application, specifically for handling attachments.
 * It provides functionality to attach and view files, search and attach files, and manage attachments.
 */
class Attachments extends Module
{
    public $attachments = [];

    public function name(): string
    {
        return 'Attachments';
    }

    public function register(): array
    {
        $this->cmd->mainMenu->on('show', function () {
            $this->notice();
        });

        return [
            'attach' => 'Attach one or more files to this conversation.',
        ];
    }

    public function onSelect(?string $key = null)
    {
        if ($key === 'attach') {
            $this->searchAndAttachFiles();
        }
        if ($key === 'attach:view') {
            $this->viewAttachments();
        }
    }

    public function viewAttachments()
    {
        foreach ($this->attachments as $key => $x) {
            $this->cmd->comment($key);
            $this->cmd->comment('----');
            $this->cmd->line($x);
            $this->cmd->newLine();
        }

        if (count($this->attachments) === 0) {
            $this->cmd->comment('No attachments');
        }

        $this->cmd->newLine();
    }

    public function clearAttachments()
    {
        $this->attachments = [];

        $this->cmd->comment('Attachments cleared');
        $this->cmd->newLine();
    }

    public function addAttachment($key, $value)
    {
        $base = basename($key);
        $this->cmd->comment("Attaching $base");
        $this->attachments[$key] = $value;
        $this->setAttachmentsToChatHistory();
    }

    public function removeAttachment($key)
    {
        unset($this->attachments[$key]);
    }

    public function notice()
    {
        if (count($this->attachments) > 0) {
            $count = count($this->attachments);
            $this->cmd->info("You have $count attachments:");
            echo collect($this->attachments)->keys()->map(fn ($x) => '- '.basename($x))->implode(PHP_EOL);
            $this->cmd->newLine(2);
        }
    }

    public function searchAndAttachFiles()
    {
        $this->cmd->info('Type something to search for a file to attach');
        $this->cmd->line("Search and end with '*' to include all matching files");
        $this->cmd->newLine();
        $this->cmd->line("exit   - Press enter or type 'exit' to discard");
        $this->cmd->line('view   - to view the current attachments');
        $this->cmd->line('clear  - to clear the current attachments');

        while (true) {
            $hasWildcard = false;

            $file = $this->cmd->askWithCompletion('Search', function ($x) use (&$hasWildcard) {
                if (! $x) {
                    return [];
                }
                if (str($x)->contains('=>')) {
                    return [];
                }

                $hasWildcard = str($x)->contains('*');
                // $x = str_replace('*', '', $x);

                $files = $this->search($x);

                return $files ?? [];
            });

            if ($file === 'view') {
                $this->viewAttachments();

                continue;
            }
            if ($file === 'clear') {
                $this->clearAttachments();

                continue;
            }

            if (! $hasWildcard) {
                if (! $this->addAttachmentFromFile($file)) {
                    break;
                }
            } else {
                $query = (string) str($file)->before('    => ');
                $files = $this->search($query);

                foreach ($files as $file) {
                    $this->addAttachmentFromFile($file);
                }
            }

            $this->setAttachmentsToChatHistory();
        }
    }

    public function search($search)
    {
        $files = [];
        $limit = config('synth.search_limit', 10);
        $base = config('synth.file_base', base_path());
        $excludePattern = config('synth.search_exclude_pattern', '/vendor|storage|node_modules|build|.git|.env/i');
        $count = 0;

        /**
         * @var \SplFileInfo $file
         */
        foreach (files_in($base, $search, excludePattern: $excludePattern) as $file) {
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getRealPath();
            // Make it relative to base_path
            $path = str_replace($base.'/', '', $path);

            $files[] = $search.'    => '.$path;

            $count++;

            if ($count >= $limit) {
                break;
            }
        }

        return $files;
    }

    public function addAttachmentFromFile($file)
    {
        $query = (string) str($file)->before('    => ');
        $filename = (string) str($file)->after('    => ');

        if ($query === 'exit') {
            return false;
        }

        if (! $filename) {
            return false;
        }

        try {
            $base = config('synth.file_base', base_path());
            $contents = file_get_contents($base.'/'.$filename);
        } catch (\Throwable $th) {
            $this->cmd->error("Could not find file $filename");

            return true;
        }

        $this->addAttachment($filename, $contents);

        return true;
    }

    public function addAttachmentFromMessage($key, ChatMessage $message)
    {
        $content = $message->content;
        $args = $message->function_call['arguments'] ?? '';

        $this->addAttachment($key, $content.$args);
    }

    public function setAttachmentsToChatHistory()
    {
        $history = $this->cmd->synth->ai->getHistory();

        $found = false;
        /**
         * @var ChatMessage $message
         */
        foreach ($history as &$message) {
            if ($message->role == 'user' && str($message->content)->contains('[attached_files]')) {
                $message->content = $this->getAttachmentsAsString();
                $found = true;
            }
        }

        if (! $found) {
            $this->cmd->synth->ai->addHistory(new ChatMessage('user', $this->getAttachmentsAsString()));
            $history = $this->cmd->synth->ai->getHistory();
        }

        // ray($history);
    }

    public function getAttachments(string $key = null)
    {
        return $key ? $this->attachments[$key] ?? null : $this->attachments;
    }

    public function getAttachmentsAsString()
    {
        $string = '[attached_files]'.PHP_EOL;

        foreach ($this->attachments as $key => $value) {
            $string .= "$key:".PHP_EOL;
            $string .= $value.PHP_EOL;
        }

        $string .= '[/attached_files]'.PHP_EOL;

        return $string;
    }
}
