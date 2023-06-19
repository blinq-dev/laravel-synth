<?php

$template = file_get_contents(__DIR__.'/models.template.php');

return "
Use below laravel models template to create the models:

$template

--
Output the WHOLE file

";
