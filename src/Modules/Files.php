<?php

namespace Blinq\Synth\Modules;

/**
 * This file is a module in the Synth application, specifically for handling file operations.
 * It provides functionality to write files to the filesystem, manage unwritten files, and clear files.
 */
class Files extends Module
{
    public $files = [];

    public function name(): string
    {
        return 'Files';
    }

    public function register(): array
    {
        $this->cmd->mainMenu->on('show', function () {
            $this->notice();
        });

        return [
            'write' => 'Write files to the filesystem',
        ];
    }

    public function notice()
    {
        if (count($this->files) > 0) {
            $count = count($this->files);
            $this->cmd->info("You have $count unwritten files:");
            echo collect($this->files)->keys()->map(fn ($x) => '- '.$x)->implode(PHP_EOL);
            $this->cmd->newLine(2);
        }
    }

    public function onSelect(string $key = null)
    {
        $this->write();
    }

    public function write()
    {
        $this->cmd->info('Writing files to the filesystem...');
        $this->cmd->newLine();

        $base = config('synth.file_base', base_path());

        foreach ($this->files as $file => $contents) {
            $basename = basename($file);

            $this->cmd->comment($file);
            $this->cmd->comment('----');
            $this->cmd->line($contents);

            $fullFile = $base.'/'.$file;

            $fileExists = file_exists($fullFile);

            if ($this->cmd->confirm("Write $basename?".($fileExists ? ' (File already exists)' : ''), ! $fileExists)) {
                $file = $this->cmd->askWithCompletion('Write path', [$file], $file);

                if ($file) {
                    $file = $base.'/'.$file;
                    $directory = dirname($file);

                    if (! is_dir($directory)) {
                        mkdir($directory, 0777, true);
                    }

                    file_put_contents($file, $contents);

                    $this->cmd->info("Written $file");
                }
            }
        }

        $this->clearFiles();
        $this->cmd->info('Done!');
        $this->cmd->newLine();
    }

    public function addFile($name, $contents)
    {
        $this->files[$name] = $contents;
    }

    public function removeFile($name)
    {
        unset($this->files[$name]);
    }

    public function clearFiles()
    {
        $this->files = [];
    }
}
