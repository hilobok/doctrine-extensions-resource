<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Anh\DoctrineResource\ORM\ResourceRepositoryFactory;
use Anh\DoctrineResource\ORM\EventListener\LoadMetadataSubscriber;
use Anh\Paginator\Paginator;

// define resources
$resources = [
    'article' => [ // resource name
        'model' => 'Anh\BlahBundle\Entity\Article',  // model (required)
        'repository' => 'Some\Other\Name\Space\Repository', // you can override resource repository here (optional)
        'interface' => 'Another\Lib\Interface', // for Doctrine ResolveTargetEntityListener (optional, can be array, not implemented yet)
        'rules' => [ // rules for this resource (optional)
            'isPublished' => [
                'isDraft' => false,
                'r.publishedSince <= current_timestamp()',
            ],
        ],
    ],
    'category' => [
        'model' => 'Anh\PaperBundle\Entity\Category',
        'repository' => 'Anh\PaperBundle\Entity\CategoryRepository',
    ],
];

// create config for object manager
$config = new Configuration();
/* set up orm config */

// set custom repository factory to inject paginator into repository
$repositoryFactory = new ResourceRepositoryFactory($resources, new Paginator());
$config->setRepositoryFactory($repositoryFactory);

// create entity manager
$entityManager = EntityManager::create([/* connection params */], $config);

// create event dispatcher
$eventDispatcher = new EventDispatcher();
/* event dispatcher setup */

// add doctrine event subscriber
$entityManager->getEventManager()->addEventSubscriber(new LoadMetadataSubscriber($resources));

// create factory for resource manager
$resourceManagerFactory = new ResourceManagerFactory($resources, $eventDispatcher);

// create resource manager
$articleManager = $resourceManagerFactory->create('article', $entityManager);

// resource manager usage
$article = $articleManager->createResource();
$article->setTitle('This is so test title');
$articleManager->create($article);
$article->setTitle('This is test title');
$articleManager->update($article);

$articleManager->delete($article);

// resource repository usage
$articleRepository = $articleManager->getRepository();

// paginate published articles
$publishedArticles = $articleRepository->paginate(1, 20, ['[isPublished]']);

// fetch articles with complex criteria
$ratedArticles = $articleRepository->fetch(
    [ // criteria
        '%rating' => [ '>' => 10 ],
        '[isPublished]',
    ],
    [ // sorting
        'rating' => 'desc'
    ],
    5 // limit
);
