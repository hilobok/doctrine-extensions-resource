<?php

namespace Anh\DoctrineResource\ORM;

use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Anh\DoctrineResource\RuleResolver;

class ResourceRepositoryFactory extends DefaultRepositoryFactory
{
    protected $paginator;

    protected $ruleResolver;

    /**
     * Constructor
     * @param mixed $paginator Paginator, should be compatible with ResourcePaginatorInterface.
     */
    public function __construct($paginator, RuleResolver $ruleResolver = null)
    {
        $this->paginator = $paginator;
        $this->ruleResolver = $ruleResolver ?: new RuleResolver();
    }

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $repository = parent::createRepository($entityManager, $entityName);

        if ($repository instanceof ResourceRepository) {
            $repository
                ->setPaginator($this->paginator)
                ->setRuleResolver($this->ruleResolver)
            ;
        }

        return $repository;
    }
}
