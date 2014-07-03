<?php

namespace spec\Anh\DoctrineResource\ORM;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityManager as ORMObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anh\DoctrineResource\Event\ResourceEvent;

class ResourceManagerSpec extends ObjectBehavior
{
    public function let(ORMObjectManager $manager, EventDispatcherInterface $eventDispatcher, ResourceEvent $event)
    {
        $eventDispatcher->dispatch(Argument::any(), Argument::any())->willReturn($event);
        $this->beConstructedWith($manager, $eventDispatcher, 'StdClass', 'entity', 'entity');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ORM\ResourceManager');
    }

    public function it_should_delete_resource_by_id(ORMObjectManager $manager)
    {
        $manager->getReference('StdClass', 1)->shouldBeCalled()->willReturn(2);
        $manager->remove(2)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $this->delete(1);
    }
}
