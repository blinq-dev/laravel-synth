# This is my package synth

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blinq/synth.svg?style=flat-square)](https://packagist.org/packages/blinq/synth)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/blinq-dev/laravel-synth/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/blinq/synth/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/blinq-dev/laravel-synth/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/blinq-dev/laravel-synth/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/blinq/synth.svg?style=flat-square)](https://packagist.org/packages/blinq/synth)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require blinq/synth
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="synth-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$synth = new Blinq\Synth();
echo $synth->echoPhrase('Hello, Blinq!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lennard](https://github.com/lennardv2)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## TODO
- [ ] Refactor the code a bit
- [ ] Non-interactive mode
- [x] Cancel a running chatgpt answer with ctrl+c
