<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceManagerFactory
{
    protected $resources;

    protected $manager;

    protected $eventDispatcher;

    protected $classResolver;

    public function __construct(
        array $resources,
        ObjectManager $manager,
        EventDispatcherInterface $eventDispatcher,
        ResourceManagerClassResolver $classResolver = null
    ) {
        $this->resources = $resources;
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->classResolver = $classResolver ?: new ResourceManagerClassResolver();
    }

    public function create($resourceName)
    {
        $resource = $this->getResource($resourceName);

        if ($resource === null) {
            throw new \InvalidArgumentException(
                sprintf("Resource '%s' not defined.", $resourceName)
            );
        }

        if (!isset($resource['model'])) {
            throw new \InvalidArgumentException(
                sprintf("Model for resource '%s' not defined.", $resourceName)
            );
        }

        $managerClass = $this->classResolver->resolve($this->manager);

        return new $managerClass(
            $this->manager,
            $this->eventDispatcher,
            $resource['model'],
            $resourceName
        );
    }

    protected function getResource($resourceName)
    {
        return isset($this->resources[$resourceName]) ? $this->resources[$resourceName] : null;
    }
}
