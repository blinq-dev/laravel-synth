<?php

return [
    'functions' => [
        [
            'name' => 'save_files',
            'description' => 'Save the files in laravel',
            'parameters' => [
                "type" => "object",
                "properties" => [
                    "files" => [
                        "type" => "array",
                        "items" => [
                            "type" => "object",
                            "required" => [
                                "name",
                                "contents"
                            ],
                            "properties" => [
                                "name" => [
                                    "type" => "string",
                                    "description" => "The full filename of the file, starting from the laravel base path. Ex: app/Models/Model.php"
                                ],
                                "contents" => [
                                    "type" => "string",
                                    "description" => "The WHOLE contents of the file."
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
