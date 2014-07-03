<?php

namespace Anh\DoctrineResource\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Repository\DefaultRepositoryFactory;
use Doctrine\ODM\MongoDB\DocumentManager;
use Anh\DoctrineResource\ResourceRepositoryFactoryTrait;

/**
 * Doctrine MongoDB ODM don't have ability to specify custom repository factory (at least in 1.0.0-BETA10)
 * Was proposed in PR #892 https://github.com/doctrine/mongodb-odm/pull/892
 */
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
