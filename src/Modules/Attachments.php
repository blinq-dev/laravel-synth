<?php

namespace Blinq\Synth\Modules;

use Blinq\LLM\Entities\ChatMessage;

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
            'attach:view' => 'View the attached files.',
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
            $this->cmd->line($key);
            $this->cmd->line('----');
            $this->cmd->line($x);
            $this->cmd->newLine();

            $this->cmd->info('Press enter to continue');
            $this->cmd->ask('You');
        }
    }

    public function addAttachment($key, $value)
    {
        $base = basename($key);
        $this->cmd->comment("Attaching $base");
        $this->attachments[$key] = $value;
        $this->setAttachmentsToChatHistory();
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
        while (true) {
            $hasWildcard = false;
            $file = $this->cmd->askWithCompletion('Search for a file to include (end with * to match multiple files)', function ($x) use (&$hasWildcard) {
                if (! $x) {
                    return [];
                }
                if (str($x)->contains('=>')) {
                    return [];
                }

                $hasWildcard = str($x)->contains('*');
                $x = str_replace('*', '', $x);

                $files = $this->search($x);

                return $files ?? [];
            });

            if (! $hasWildcard) {
                if (! $this->addAttachmentFromFile($file)) {
                    break;
                }
            } else {
                $search = (string) str($file)->before('*');

                $files = $this->search($search);

                foreach ($files as $file) {
                    $this->addAttachmentFromFile($file);
                }
            }

            $this->setAttachmentsToChatHistory();
            $this->cmd->newLine(2);
            $this->cmd->comment("Type something to refine, press enter to save and continue, type 'exit' to discard");
        }
    }

    public function search($search)
    {
        $files = [];
        /**
         * @var \SplFileInfo $file
         */
        foreach (files_in(base_path(), $search, excludePattern: '/vendor|storage|node_modules|.git|.env/i') as $file) {
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getRealPath();
            // Make it relative to base_path
            $path = str_replace(base_path().'/', '', $path);

            $files[] = $search.'    => '.$path;
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
            $contents = file_get_contents(base_path($filename));
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
            if ($message->role == 'user' && str($message->content)->contains('[x84y2jd]')) {
                $message->content = $this->getAttachmentsAsString();
                $found = true;
            }
        }

        if (! $found) {
            $this->cmd->synth->ai->addHistory(new ChatMessage('user', $this->getAttachmentsAsString()));
            $history = $this->cmd->synth->ai->getHistory();
        }

        ray($history);
    }

    public function getAttachments(string $key = null)
    {
        return $key ? $this->attachments[$key] ?? null : $this->attachments;
    }

    public function getAttachmentsAsString()
    {
        $string = '[x84y2jd]'.PHP_EOL;

        foreach ($this->attachments as $key => $value) {
            $string .= "$key:".PHP_EOL;
            $string .= $value.PHP_EOL;
        }

        $string .= '[/x84y2jd]'.PHP_EOL;

        return $string;
    }
}
