# Doctrine2 resource extension

[![Build Status](https://travis-ci.org/hilobok/doctrine-extensions-resource.svg?branch=master)](https://travis-ci.org/hilobok/doctrine-extensions-resource) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d/mini.png)](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d)

## Installation
```bash
$ php composer.phar require 'anh/doctrine-extensions-resource:0.2.*'
```
## Usage
See [orm-example.php](https://github.com/hilobok/doctrine-extensions-resource/blob/master/orm-example.php).

## Symfony integration
There is a [bundle](https://github.com/hilobok/AnhDoctrineResourceBundle) or [package](https://packagist.org/packages/anh/doctrine-resource-bundle) for that.

## Advanced criteria format
You can use advanced criteria format for filtering. It's not limited to equal comparison only. For example:

```php
$criteria = [
    'section' => 'articles', // old school
    '%rating' => [ '>' => 10 ],
    '#or' => [
        '%title-1' => [ 'like' => '%word.' ],
        '%title-2' => [ 'like' => 'Some%' ],
        '#and' => [
            'role' => [ 'moderator', 'editor' ],
            '#or' => [
                'status' => 'fixed',
                'isDraft' => true,
            ],
        ],
    ],
];
```

If the field name starts with `%` then advanced format for field should follow. It consists of the array with a single element, where key is the comparison operator and value is parameter for operator.
Symbol `#` with following `and` or `or` is used for changing comparison type.
If you need a few criteria for the same field, simply add a dash followed by a number. Apply the same for the comparison types. Comparison types can be nested.
Common operators are: `>`, `<`, `>=`, `<=`, `<>` and others, for full list refer to `QueryBuilderAdapter` of each driver.

This advanced criteria format is valid for all available drivers (ORM, PHPCR-ODM, MongoDB-ODM). Note: not all operators are available for each driver.

## Credits
Inspired by [SyliusResourceBundle](https://github.com/Sylius/SyliusResourceBundle) from [Sylius](http://sylius.org).

## Versioning
Library uses [semantic versioning](http://semver.org/).

## License
MIT
