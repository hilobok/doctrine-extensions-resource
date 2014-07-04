<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectRepository;

trait ResourceRepositoryFactoryTrait
{
    protected $resources;

    protected $paginator;

    /**
     * Constructor
     * @param mixed $paginator Paginator, should be compatible with ResourcePaginatorInterface.
     */
    public function __construct($resources, $paginator = null)
    {
        $this->resources = $resources;
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     * Injects paginator into repository if it's an instance of ResourceRepository.
     */
    protected function injectResourceServices(ObjectRepository $repository)
    {
        if ($repository instanceof ResourceRepositoryInterface) {
            $modelClass = $repository->getClassName();
            $resource = $this->findResourceByModelClass($modelClass);

            $resource += array(
                'rules' => array(),
            );

            $repository
                ->setPaginator($this->paginator)
                ->setRules($resource['rules'])
            ;
        }

        return $repository;
    }

    protected function findResourceByModelClass($modelClass)
    {
        foreach ($this->resources as $resource) {
            if ($resource['model'] == $modelClass) {
                return $resource;
            }
        }

        return array();
    }
}
