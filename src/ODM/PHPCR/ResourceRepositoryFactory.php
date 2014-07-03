<?php

namespace Anh\DoctrineResource\ODM\PHPCR;

use Doctrine\ODM\PHPCR\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\PHPCR\DocumentManager;
use Anh\DoctrineResource\ResourceRepositoryFactoryTrait;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    use ResourceRepositoryFactoryTrait;

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(DocumentManager $documentManager, $documentName)
    {
        return $this->injectResourceServices(parent::createRepository($documentManager, $documentName));
    }
}
