# Synth for Laravel
(Not an official Laravel package)

<img width="693" alt="Synth for Laravel" src="https://github.com/blinq-dev/laravel-synth/assets/168357/7dccb9ba-1db5-4d6f-9a41-fde6f99a2446">

Synth is a Laravel tool that helps you generate code and perform various tasks in your Laravel application. It leverages the power of OpenAI's GPT language model to provide an interactive and intelligent development experience.

## Demo

### Architect -> create a todo app 😎🏗️📝
https://github.com/blinq-dev/laravel-synth/assets/168357/78116a9a-1f37-4410-9f20-f9fe6573196b

### Attach files and create a readme.md 📎📄✍️
https://github.com/blinq-dev/laravel-synth/assets/168357/70bc57a5-0aa8-439f-95af-fb02685e3756

## Installation

1. Install the Synth package using Composer:

   ```bash
   composer require blinq/synth
   ```

2. Publish the Synth configuration file:

   ```bash
   php artisan vendor:publish --tag=synth-config
   ```

   Here you can change the used model (gpt4 versus gpt3)

3. Set your OpenAI API key in the `.env` file:

   ```   OPENAI_KEY=YOUR_API_KEY   ```

## Usage
To use Synth, simply run the `synth` command:

```bash
php artisan synth
```

This will open the Synth CLI, where you can interact with the GPT model and perform various tasks.

### Features 🌟
- Automatically switch from small to large model when needed (gpt-3.5-turbo vs gpt-3.5-turbo-16k) 🔄
- Include the whole database schema as an attachment
- Uses the functions API of OpenAI 👨‍💻
- Cancel generation with Ctrl+C 🚫⌨
- Attachments: Attach files to the conversation with GPT. 🗂️
- Architect: Brainstorm and generate a new application architecture. 💡🏛
- Chat: Chat with GPT to get responses and perform actions. 💬
- Make: Forces GPT to generate files for the question asked. 📂
- Migrations: Generate migrations for your application. 📦
- Models: Generate models for your application. 📈
- Files: Write files to the filesystem. 🖊️

You can select a module from the main menu and follow the prompts to perform the desired actions.

> Note: Some modules require a previous step to be completed, such as creating an architecture before generating migrations or models.

## Writing Your Own Modules

Synth allows you to extend its functionality by writing your own modules. A module is a class that implements the necessary methods to register and handle specific actions.

To create a new module, follow these steps:

1. Create a new PHP class that extends the `Module` class.
2. Implement the `name` method to define the name of your module.
3. Implement the `register` method to define the actions provided by your module.
4. Implement the `onSelect` method to handle the selected action.

Here is an example of a custom module implementation:

```php
use Blinq\Synth\Modules\Module;

/**
 * Class MyModule
 * 
 * @propery \Blinq\Synth\Commands\SynthCommand $cmd
 */
class MyModule extends Module
{
    public function name(): string
    {
        return 'MyModule';
    }

    public function register(): array
    {
        return [
            'action1' => 'Perform Action 1',
            'action2' => 'Perform Action 2',
        ];
    }

    public function onSelect(?string $key = null)
    {
        $this->cmd->info("You selected: {$key}");

        $synth = $this->cmd->synth;

        if ($key === 'action1') {
            // Handle Action 1
            while (true) {
                $input = $this->cmd->ask("You");

                // Send the input to GPT
                $synth->chat($input, [
                    // ... The OpenAI Chat options

                    // If you want a function to be called by GPT
                    'function_call' => ['name' => 'some_function'], // Forces the function call
                    'functions' => [
                        [
                            'name' => 'some_function',
                            'description' => 'Description of the function',
                            'parameters' => [
                                // ..schema
                            ]
                        ]
                    ]
                ]);

                Functions::register('some_function', function (SynthCommand $cmd, $args, $asSpecified, $inSchema) { // etc..
                    // Do something with the call
                });

                // This will parse the json result and call the function if needed
                $synth->handleFunctionsForLastMessage();

                // Just retrieve the last message
                $lastMessage = $synth->getLastMessage();

                // Echo it's contents
                echo $lastMessage->content;

                // Or it's raw function_call
                dump($lastMessage->function_call);

                if (!$input || $input == 'exit') {
                    break;
                }
            }
        }
        if ($key === 'action2') {
            // Handle Action 2
        }
    }
}
```

You can then register your custom module in the `Modules` class within the Synth package and use it in the CLI interface:

```php
use Blinq\Synth\Modules;

Modules::register(MyModule::class);
```
