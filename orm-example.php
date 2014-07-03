<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Anh\DoctrineResource\ORM\ResourceRepositoryFactory;
use Anh\DoctrineResource\ORM\EventListener\LoadMetadataSubscriber;
use Anh\DoctrineResource\RuleResolver;
use Anh\Paginator\Paginator;

// create entity manager configuration
$config = new Configuration();
/* set up orm config */

// add criteria groups
$ruleResolver = new RuleResolver();
$ruleResolver->add('article', 'isPublished', ['isDraft' => false, 'a.publishedSince <= current_timestamp()']);

// set custom repository factory to inject paginator and rule resolver into repository
$repositoryFactory = new ResourceRepositoryFactory(new Paginator(), $ruleResolver);
$config->setRepositoryFactory($repositoryFactory);

// create entity manager
$entityManager = EntityManager::create([/* connection params */], $config);

// create event dispatcher
$eventDispatcher = new EventDispatcher();
/* event dispatcher setup */

// define resources
$resources = [
    'article' => [
        'model' => 'Anh\BlahBundle\Entity\Article',
        'interface' => 'Anh\Taggable\TaggableResourceInterface',
        'alias' => 'a',
    ],
    'paper' => [
        'model' => 'Anh\PaperBundle\Entity\Paper',
        'repository' => 'Anh\PaperBundle\Entity\PaperRepository'
    ],
];

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

// paginate articles with criteria group
$publishedArticles = $articleRepository->paginate(1, 20, array('[isPublished]'));

$ratedArticles = $articleRepository->fetch(['%rating' => [ '>' => 10 ]], ['rating' => 'desc'], 5);
