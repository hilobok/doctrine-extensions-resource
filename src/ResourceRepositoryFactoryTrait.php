<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectRepository;

trait ResourceRepositoryFactoryTrait
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
    protected function injectResourceServices(ObjectRepository $repository)
    {
        if ($repository instanceof ResourceRepositoryInterface) {
            $repository
                ->setPaginator($this->paginator)
                ->setRuleResolver($this->ruleResolver)
            ;
        }

        return $repository;
    }
}