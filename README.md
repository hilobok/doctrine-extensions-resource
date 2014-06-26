# Doctrine2 resource extension

[![Build Status](https://travis-ci.org/hilobok/doctrine-extensions-resource.svg?branch=master)](https://travis-ci.org/hilobok/doctrine-extensions-resource) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d/mini.png)](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d)

## Installation
```bash
$ php composer.phar require 'anh/doctrine-extensions-resource:0.1.0'
```
## Usage
See [orm-example.php](https://github.com/hilobok/doctrine-extensions-resource/blob/master/orm-example.php).

## Symfony integration
There is a [bundle](https://github.com/hilobok/AnhDoctrineResourceBundle) or [package](https://packagist.org/packages/anh/doctrine-resource-bundle) for that.

## Advanced criteria format
You can use advanced criteria format for filtering, it's not limited only to equal comparison, you can use a lot more. For example:

```php
$criteria = [
    'section' => 'articles',
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

When field name starts with `%` it means that advanced format should follow. Symbol `#` with following `and` or `or` used for changing comparison type.
If you need to have few comparisons for the same field, simply append number to it, same applies to comparison types. Comparison types can be nested.
Common operators are: `>`, `<`, `>=`, `<=`, `<>` and more, for full list take a look at `QueryBuilderAdapter` in corresponding driver.

This advanced criteria format applies to all available drivers (ORM, PHPCR-ODM, MongoDB-ODM). Note that not all operators available for every driver.

## Credits
Inspired by [SyliusResourceBundle](https://github.com/Sylius/SyliusResourceBundle) from [Sylius](http://sylius.org).

## Versioning
Library uses [semantic versioning](http://semver.org/).

## License
MIT
