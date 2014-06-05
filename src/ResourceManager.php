<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anh\DoctrineResource\Event\ResourceEvent;

class ResourceManager
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $resourceName;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $modelClass, $resourceName)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->modelClass = $modelClass;
        $this->resourceName = $resourceName;
    }

    public function createResource()
    {
        return new $this->modelClass();
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getRepository()
    {
        return $this->manager->getRepository($this->modelClass);
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function create($resource, $flush = true)
    {
        if (is_array($resource)) {
            return $this->createList($resource, $flush);
        }

        $event = $this->dispatchEvent(ResourceEvent::PRE_CREATE, $resource);
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->manager->persist($resource);
        if ($flush) {
            $this->manager->flush();
        }

        $this->dispatchEvent(ResourceEvent::POST_CREATE, $resource);

        return $resource;
    }

    public function createList(array $resources, $flush = true)
    {
        $created = array();

        foreach ($resources as $key => $resource) {
            $created[$key] = $this->create($resource, false);
        }

        if ($flush) {
            $this->manager->flush();
        }

        return $created;
    }

    public function update($resource, $flush = true)
    {
        if (is_array($resource)) {
            return $this->updateList($resource, $flush);
        }

        $event = $this->dispatchEvent(ResourceEvent::PRE_UPDATE, $resource);
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->manager->persist($resource);
        if ($flush) {
            $this->manager->flush();
        }

        $this->dispatchEvent(ResourceEvent::POST_UPDATE, $resource);

        return $resource;
    }

    public function updateList(array $resources, $flush = true)
    {
        $updated = array();

        foreach ($resources as $key => $resource) {
            $updated[$key] = $this->update($resource, false);
        }

        if ($flush) {
            $this->manager->flush();
        }

        return $updated;
    }

    public function delete($resource, $flush = true)
    {
        if (is_array($resource)) {
            return $this->deleteList($resource, $flush);
        }

        $event = $this->dispatchEvent(ResourceEvent::PRE_DELETE, $resource);
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->manager->remove($resource);
        if ($flush) {
            $this->manager->flush();
        }

        $this->dispatchEvent(ResourceEvent::POST_DELETE, $resource);

        return $resource;
    }

    public function deleteList(array $resources, $flush = true)
    {
        $deleted = array();

        foreach ($resources as $key => $resource) {
            $deleted[$key] = $this->delete($resource, false);
        }

        if ($flush) {
            $this->manager->flush();
        }

        return $deleted;
    }

    protected function dispatchEvent($eventName, $resource)
    {
        return $this->eventDispatcher->dispatch(
            $this->getEventName($eventName),
            new ResourceEvent($resource)
        );
    }

    protected function getEventName($eventName)
    {
        return sprintf('%s.%s.%s', 'anh_resource', $this->resourceName, $eventName);
    }
}
