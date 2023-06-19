<?php

use Blinq\Synth\Commands\SynthCommand;
use Blinq\Synth\Functions;

Functions::register('save_migrations', function(SynthCommand $cmd, $migrations = [])
{
    foreach($migrations as $migration) {
        $name = $migration['name'];
        // Normalize the name
        $name = str($name)
            ->replaceMatches('/[^a-zA-Z_]/', '')
            ->studly()
            ->beforeLast("php");

        $slug = str($name)->snake()->slug('_');
        $file = date('Y_m_d_His') . "_" . $slug . ".php";

        $contents = $migration['contents'];

        // Normalize the file
        // Check if it has <?php at the start (add it)
        if (!str($contents)->startsWith('<?php')) {
            $contents = "<?php\n\n" . $contents;
        }

        echo PHP_EOL;
        if ($cmd->confirm("Do you want to save the following migration: $file", true)) {
            
            // Run this: artisan make:migration $name
            // Save the contents to the migration file
            
            $migrationFile = database_path("migrations/" . $file);

            file_put_contents($migrationFile, $contents);

            $cmd->modules->get("Attachments")?->addAttachment($migrationFile, $contents);
        }
    }
});