<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Anh\DoctrineResource\ResourceRepositoryFactoryTrait;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    use ResourceRepositoryFactoryTrait;

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        return $this->injectResourceServices(parent::createRepository($entityManager, $entityName));
    }
}
