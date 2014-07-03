<?php

namespace spec\Anh\DoctrineResource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anh\DoctrineResource\Event\ResourceEvent;

class ResourceManagerSpec extends ObjectBehavior
{
    public function let(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, ResourceEvent $event)
    {
        $eventDispatcher->dispatch(Argument::any(), Argument::any())->willReturn($event);
        $this->beConstructedWith($manager, $eventDispatcher, 'StdClass', 'entity', 'entity');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ResourceManager');
    }

    public function it_should_create_resource()
    {
        $this->createResource()->shouldBeAnInstanceOf('StdClass');
    }

    public function it_should_return_model_class()
    {
        $this->getModelClass()->shouldReturn('StdClass');
    }

    public function it_should_return_resource_name()
    {
        $this->getResourceName()->shouldReturn('entity');
    }

    public function it_should_return_repository(ObjectManager $manager)
    {
        $manager->getRepository('StdClass')->shouldBeCalled();
        $this->getRepository();
    }

    public function it_should_return_manager(ObjectManager $manager)
    {
        $this->getManager()->shouldReturn($manager);
    }

    public function it_should_dispatch_events_on_create(\StdClass $entity, EventDispatcherInterface $eventDispatcher, ResourceEvent $event)
    {
        $eventDispatcher->dispatch('anh_resource.entity.pre_create', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_create', Argument::any())->shouldBeCalled()->willReturn($event);
        $this->create($entity);
    }

    public function it_should_create_with_flush_by_default(\StdClass $entity, ObjectManager $manager)
    {
        $manager->persist($entity)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->create($entity);
    }

    public function it_should_create_without_flush(\StdClass $entity, ObjectManager $manager)
    {
        $manager->persist($entity)->shouldBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->create($entity, false);
    }

    public function it_should_not_create_when_event_propagation_is_stopped(
        \StdClass $entity,
        EventDispatcherInterface $eventDispatcher,
        ResourceEvent $event,
        ObjectManager $manager
    ) {
        $event->isPropagationStopped()->willReturn(true);
        $eventDispatcher->dispatch('anh_resource.entity.pre_create', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_create', Argument::any())->shouldNotBeCalled();
        $manager->persist($entity)->shouldNotBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->create($entity);
    }

    public function it_should_be_able_to_create_array_of_resources(ObjectManager $manager)
    {
        $manager->persist(1)->shouldBeCalled();
        $manager->persist(2)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->create(array(1, 2));
    }

    public function it_should_dispatch_events_on_update(\StdClass $entity, EventDispatcherInterface $eventDispatcher, ResourceEvent $event)
    {
        $eventDispatcher->dispatch('anh_resource.entity.pre_update', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_update', Argument::any())->shouldBeCalled()->willReturn($event);
        $this->update($entity);
    }

    public function it_should_update_with_flush_by_default(\StdClass $entity, ObjectManager $manager)
    {
        $manager->persist($entity)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->update($entity);
    }

    public function it_should_update_without_flush(\StdClass $entity, ObjectManager $manager)
    {
        $manager->persist($entity)->shouldBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->update($entity, false);
    }

    public function it_should_not_update_when_event_propagation_is_stopped(
        \StdClass $entity,
        EventDispatcherInterface $eventDispatcher,
        ResourceEvent $event,
        ObjectManager $manager
    ) {
        $event->isPropagationStopped()->willReturn(true);
        $eventDispatcher->dispatch('anh_resource.entity.pre_update', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_update', Argument::any())->shouldNotBeCalled();
        $manager->persist($entity)->shouldNotBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->update($entity);
    }

    public function it_should_be_able_to_update_array_of_resources(ObjectManager $manager)
    {
        $manager->persist(1)->shouldBeCalled();
        $manager->persist(2)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->update(array(1, 2));
    }

    public function it_should_dispatch_events_on_delete(\StdClass $entity, EventDispatcherInterface $eventDispatcher, ResourceEvent $event)
    {
        $eventDispatcher->dispatch('anh_resource.entity.pre_delete', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_delete', Argument::any())->shouldBeCalled()->willReturn($event);
        $this->delete($entity);
    }

    public function it_should_delete_with_flush_by_default(\StdClass $entity, ObjectManager $manager)
    {
        $manager->remove($entity)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->delete($entity);
    }

    public function it_should_delete_without_flush(\StdClass $entity, ObjectManager $manager)
    {
        $manager->remove($entity)->shouldBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->delete($entity, false);
    }

    public function it_should_not_delete_when_event_propagation_is_stopped(
        \StdClass $entity,
        EventDispatcherInterface $eventDispatcher,
        ResourceEvent $event,
        ObjectManager $manager
    ) {
        $event->isPropagationStopped()->willReturn(true);
        $eventDispatcher->dispatch('anh_resource.entity.pre_delete', Argument::any())->shouldBeCalled()->willReturn($event);
        $eventDispatcher->dispatch('anh_resource.entity.post_delete', Argument::any())->shouldNotBeCalled();
        $manager->remove($entity)->shouldNotBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $this->delete($entity);
    }

    public function it_should_be_able_to_delete_array_of_resources(ObjectManager $manager)
    {
        $manager->remove(1)->shouldBeCalled();
        $manager->remove(2)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->delete(array(1, 2));
    }
}
