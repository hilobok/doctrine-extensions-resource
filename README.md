# Doctrine2 resource extension

[![Build Status](https://travis-ci.org/hilobok/doctrine-extensions-resource.svg?branch=master)](https://travis-ci.org/hilobok/doctrine-extensions-resource) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hilobok/doctrine-extensions-resource/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d/mini.png)](https://insight.sensiolabs.com/projects/f28bcb37-fdb1-4ec9-85b9-d9079d05552d)

Extension provides simplified and unified interface for working with resources.

## Installation
```bash
$ php composer.phar require 'anh/doctrine-extensions-resource:0.2.*'
```

## Symfony integration
There is a [bundle](https://github.com/hilobok/AnhDoctrineResourceBundle) or [package](https://packagist.org/packages/anh/doctrine-resource-bundle) for that.

## Usage

### Defining resources
Resource definition is an array with required and optional keys.

```php
$resources = [
    'article' => [ // resource name
        'model' => 'Some\Name\Space\Entity\Article', // model (required)
        'alias' => 'a', // alias for query builder (optional), will be 'resource' if omitted
        'repository' => 'Some\Other\Name\Space\Repository', // you can override resource repository here (optional)
        'interface' => 'Another\Lib\Interface', // for Doctrine ResolveTargetEntityListener (optional, can be array, not implemented yet)
    ],

    'category' => [ // another resource
        /* ... */
    ],
];
```

### Initialization
You should configure object manager to use `ResourceRepositoryFactory` in order to inject paginator and rule resolver services into repositories. Extension can use any paginator compatible with `ResourcePaginatorInterface`. About `RuleResolver` see below.

Create entity manager, event dispatcher, add event subscriber and create `ResourceManagerFactory`.

```php
<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Anh\DoctrineResource\ORM\ResourceRepositoryFactory;
use Anh\DoctrineResource\ORM\EventListener\LoadMetadataSubscriber;
use Anh\Paginator\Paginator;

// create Paginator
$paginator = new Paginator();
$repositoryFactory = new ResourceRepositoryFactory($paginator);

// create config for object manager
$config = new Configuration();
/* set up orm config here */

$config->setRepositoryFactory($repositoryFactory);

// create entity manager
$entityManager = EntityManager::create([/* connection params */], $config);

// create event dispatcher
$eventDispatcher = new EventDispatcher();
/* set up event dispatcher here */

// add doctrine event subscriber
$entityManager->getEventManager()->addEventSubscriber(new LoadMetadataSubscriber($resources));

// create factory for resource manager
$resourceManagerFactory = new ResourceManagerFactory($resources, $eventDispatcher);

```

### CRUD operations with `ResourceManager`.
Each resource type has it's own resource manager, to instantiate it you should use `ResourceManagerFactory::create()`.

```php
$articleManager = $resourceManagerFactory->create('article', $entityManager);

// basic CRUD operations
// create resource
$article = $articleManager->createResource();
$article->setTitle('This is so test title');
$articleManager->create($article);

// update resource
$article->setTitle('This is test title');
$articleManager->update($article);

// delete resource
$articleManager->delete($article);
```

### Events
Code above will generate this events:
- anh_resource.article.pre_create
- anh_resource.article.post_create
- anh_resource.article.pre_update
- anh_resource.article.post_update
- anh_resource.article.pre_delete
- anh_resource.article.post_delete

### Fetching resources with `ResourceRepository`.
`ResourceRepository` has two methods for fetching resources: `paginate()` and `fetch()`.

See [orm-example.php](https://github.com/hilobok/doctrine-extensions-resource/blob/master/orm-example.php).

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

### Advanced comparison
If the field name starts with `%` then advanced format for field should follow. It consists of the array with a single element, where key is the comparison operator and value is parameter for operator. Common operators are: `>`, `<`, `>=`, `<=`, `<>` and others, for full list refer to `QueryBuilderAdapter` of each driver.

### Comparison types
Symbol `#` with following `and` or `or` is used for changing comparison type. Comparison types can be nested.

### Multiple criteria on the same field
If you need a few criteria for the same field, simply add a dash followed by a number. Apply the same for the comparison types.

### Rules
Rule is predefined group of criteria for resource. You can define it by calling `RuleResolver::add(/* ... */);` with resource name, rule name and criteria. Then you should pass this resolver to `ResourceRepositoryFactory`.

```php
<?php

use Doctrine\ORM\Configuration;
use Anh\DoctrineResource\RuleResolver;
use Anh\DoctrineResource\ORM\ResourceRepositoryFactory;

// create config for object manager
$config = new Configuration();
/* set up config */

$paginator = /* ... */;

// add rules
$ruleResolver = new RuleResolver();
$ruleResolver->add('article', 'isPublished', ['isDraft' => false, 'a.publishedSince <= current_timestamp()']);

$repositoryFactory = new ResourceRepositoryFactory($paginator, $ruleResolver);
$config->setRepositoryFactory($repositoryFactory);
```

Now you can use rule name in square brackets as criterion for `ResourceRepository::paginate()` and `ResourceRepository::fetch()` methods.

```php
$repository = $manager->getRepository();
$publishedArticlesInSection = $repository->fetch([ '[isPublished]', 'section' => $section ]);
```

This advanced criteria format is valid for all available drivers (ORM, PHPCR-ODM, MongoDB-ODM).

### Note
Not all operators are available for each driver.

## Credits
Inspired by [SyliusResourceBundle](https://github.com/Sylius/SyliusResourceBundle) from [Sylius](http://sylius.org).

## Versioning
Library uses [semantic versioning](http://semver.org/).

## License
MIT
