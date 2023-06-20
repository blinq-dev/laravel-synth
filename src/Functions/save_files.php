<?php

use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Functions;

Functions::register('save_files', function (SynthCommand $cmd, $files = []) {
    $userPath = null;

    foreach ($files as $file) {
        $name = $file['name'] ?? null;
        $contents = $file['contents'] ?? null;

        if (! $name && ! $contents) {
            continue;
        }

        // Normalize the file
        // Check if it has <?php at the start (add it)
        if (str($name)->endsWith('php') && ! str($contents)->startsWith('<?php')) {
            $contents = "<?php\n\n".$contents;
        }

        if (str($name)->contains('blade.php')) {
            // Remove <?php
            $contents = str_replace("<?php\n\n", '', $contents);
            $contents = str_replace("<?php\n", '', $contents);
            $contents = str_replace('<?php', '', $contents);
        }

        if (! str($name)->contains('/')) {
            $userPath = $cmd->ask("$name does not seem to be in a subdirectory. Please enter the subdirectory it should be in (e.g. 'app/Models')", $userPath);
            $name = $userPath.'/'.$name;
        }

        $cmd->modules->get('Files')?->addFile($name, $contents);
        $cmd->modules->get('Attachments')?->addAttachment($name, $contents);
    }
});
