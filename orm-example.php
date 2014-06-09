<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Anh\DoctrineResource\ORM\EventListener\LoadMetadataSubscriber;
use Anh\Paginator\Paginator;

// create entity manager configuration
$config = new Configuration();
/* config setup */
// set custom repository factory to inject paginator into repository
$config->setRepositoryFactory(new RepositoryFactory(new Paginator()));

// create entity manager
$entityManager = EntityManager::create(array(/* connection params */), $config);

// create event dispatcher
$eventDispatcher = new EventDispatcher();
/* event dispatcher setup */

// define resources
$resources = array(
    'article' => array(
        'model' => 'Anh\BlahBundle\Entity\Article',
        'interface' => 'Anh\Taggable\TaggableResourceInterface'
    ),
    'paper' => array(
        'model' => 'Anh\PaperBundle\Entity\Paper',
        'repository' => 'Anh\PaperBundle\Entity\PaperRepository'
    )
);

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

$articles = $articleManager->getRepository()->paginate(1, 20, array('isPublished' => true));
