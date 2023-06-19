<?php

return [
    'functions' => [
        [
            'name' => 'save_migrations',
            'description' => 'Save the laravel migrations',
            'parameters' => [
                "type" => "object",
                "properties" => [
                    "migrations" => [
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
                                    "description" => "The name of the migration"
                                ],
                                "contents" => [
                                    "type" => "string",
                                    "description" => "The WHOLE contents of the laravel migration file"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
