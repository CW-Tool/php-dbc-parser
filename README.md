# WowStack: DBC reader/writer                [![Build status][bs-image]][bs-url]

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wowstack/php-dbc-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wowstack/php-dbc-parser/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/wowstack/php-dbc-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/wowstack/php-dbc-parser/?branch=master)

This is a [DBC][dbc] reader and writer library for PHP >= 7, allowing you to map,
create, modify and update DBC files from World of Warcraft up to version 3.3.5.

## Features

- supports all existing field types
- fully automated test suite
- configurable mappings via YAML files
- tools for inspecting, dumping

Want to talk with us? We're on [Discord][discord].

## Usage

The PHP DBC Parser supports both getting an initial idea of a DBC files structure
and dumping its content with an included command line tool based on **PHP 7**.

```bash
./bin/dbc-tool --help
```

### Inspecting DBC files

```bash
$ ./bin/dbc-tool dbc:inspect tests/data/AreaPOI.dbc

DBC Inspect
===========

Stats
-----

 The AreaPOI file contains 339 rows at 116 bytes per column, split into 29 fields.


Strings
-------

 The AreaPOI file contains 254 strings.

 2: Aerie Peak
 13: Algaz Station
 27: Angor Fortress
 42: Anvilmar
 51: Ariden's Camp
 65: Beggar's Haunt
 80: Booty Bay
 90: Boulderfist Outpost
 110: Brill
 116: Browman Mill
```

### Dumping a DBC file

```bash
$ ./bin/dbc-tool dbc:view tests/data/AreaPOI.dbc tests/data/maps/AreaPOI.yaml --rows 10

DBC Viewer
==========

Stats
-----

 The AreaPOI file contains 339 rows at 116 bytes per column, split into 29 fields.


Strings
-------

 The AreaPOI file contains 254 strings.


+------+------------+------+-----------+------------------+------------------+------------------+-------+-------+-------------+---------------------+-------------+--------------+
| id   | importance | icon | factionID | locationX        | locationY        | locationZ        | mapID | flags | areaTableID | name                | description | worldStateID |
+------+------------+------+-----------+------------------+------------------+------------------+-------+-------+-------------+---------------------+-------------+--------------+
| 792  | 3          | 4    |           | 234.85000610352  | -2127.7600097656 | 118.0950012207   | 0     | 13    |             | Aerie Peak          |             |              |
| 1032 | 0          | 6    |           | -4817.7900390625 | -2666.6999511719 | 351.1969909668   | 0     | 4     | 838         | Algaz Station       |             |              |
| 27   | 3          | 6    | 84        | -6392.6499023438 | -3158            | 299.76501464844  | 0     | 5     |             | Angor Fortress      |             |              |
| 1    | 3          | 6    |           | -6134.2797851562 | 383.76300048828  | 399.25399780273  | 0     | 5     | 132         | Anvilmar            |             |              |
| 1287 | 0          | 6    |           | -10443.299804688 | -2141.1000976562 | 90.779403686523  | 0     | 4     | 2697        | Ariden's Camp       |             |              |
| 1128 | 0          | 6    |           | -10349.700195312 | -1538.8000488281 | 92.642303466797  | 0     | 0     | 42          | Beggar's Haunt      |             |              |
| 40   | 3          | 4    |           | -14383.299804688 | 487.13900756836  | -29.561700820923 | 0     | 13    |             | Booty Bay           |             |              |
| 1010 | 0          | 6    |           | -1194.0500488281 | -2119.1599121094 | 61.880798339844  | 0     | 4     | 1858        | Boulderfist Outpost |             |              |
| 2    | 3          | 6    |           | 2249.8500976562  | 278.41384887695  | 34.113708496094  | 0     | 5     |             | Brill               |             |              |
| 1018 | 0          | 6    |           | 2483.9799804688  | -5183.7900390625 | 76.113502502441  | 0     | 4     | 2271        | Browman Mill        |             |              |
+------+------------+------+-----------+------------------+------------------+------------------+-------+-------+-------------+---------------------+-------------+--------------+
```

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

[bs-image]: https://travis-ci.org/wowstack/php-dbc-parser.svg?branch=master
[bs-url]: https://travis-ci.org/wowstack/php-dbc-parser

[dbc]: https://wowdev.wiki/DBC
[discord]: https://discord.gg/TttsRMp
