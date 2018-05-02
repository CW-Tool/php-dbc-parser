# WowStack: DBC reader/writer                [![Build status][bs-image]][bs-url]

This is a [DBC][dbc] reader and writer library for PHP >= 7, allowing you to map,
create, modify and update DBC files from World of Warcraft up to version 3.3.5.

## Features

- supports all existing field types
- fully automated test suite
- configurable mappings via YAML files
- tools for inspecting, dumping

## Development

Before committing changes, please run the PHP Code Style Fixer using:

```console
php-cs-fixer fix ./src --rules=@Symfony ; php-cs-fixer fix ./tests --rules=@Symfony
```

Whenever changing any classes, run the test suie before committing:

```console
./vendor/bin/phpunit --coverage-html _build --testdox
```

**Never** push changes which have not been run through the test suite successfully!

[bs-image]: https://build01.kogitoapp.com/api/badges/wowstack/dbc-parser/status.svg
[bs-url]: https://build01.kogitoapp.com/wowstack/dbc-parser

[dbc]: https://wowdev.wiki/DBC
