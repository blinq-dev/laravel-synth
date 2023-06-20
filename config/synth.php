<?php

// config for Blinq/Synth
return [
    // OpenAI api key
    'openai_key' => env('OPENAI_KEY'),
    // Small model is used by default
    'small_model' => 'gpt-3.5-turbo-0613',
    // Large model is switched to when it receives a content length error message from OpenAI
    'large_model' => 'gpt-3.5-turbo-16k-0613',
    // The base path to search from
    'file_base' => base_path(),
    // Keep performance in mind when setting this value
    'search_limit' => 10,
    // The pattern to exclude for the search
    'search_exclude_pattern' => '/vendor|storage|node_modules|build|.git|.env/i',
];
