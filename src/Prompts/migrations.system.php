<?php

$template = file_get_contents(__DIR__.'/migrations.template.php');

return "
    Use below laravel migrations template to create your migrations:

    $template

    --
    Output the WHOLE file including the up and down methods and proper fields.
    Include every file in right order. (To prevent: Failed to open the referenced table)
";
