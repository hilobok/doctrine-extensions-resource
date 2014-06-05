<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    protected $paginator;

    /**
     * Constructor
     * @param mixed $paginator Paginator, should be compatible with ResourcePaginatorInterface.
     */
    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $repository = parent::createRepository($entityManager, $entityName);

        if ($repository instanceof ResourceRepository) {
            $repository->setPaginator($this->paginator);
        }

        return $repository;
    }
}
