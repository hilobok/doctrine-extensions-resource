<?php

namespace spec\Anh\DoctrineResource;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityManager as ORMObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceManagerFactorySpec extends ObjectBehavior
{
    public function let(EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith(
            array(
                'article' => array(
                    'model' => 'Article/Entity'
                ),
                'car' => array(
                )
            ),
            $eventDispatcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Anh\DoctrineResource\ResourceManagerFactory');
    }

    public function it_should_create_manager(ORMObjectManager $manager)
    {
        $this->create('article', $manager)->shouldHaveType('Anh\DoctrineResource\ResourceManager');
    }

    public function it_should_throw_exception_on_undefined_resource(ORMObjectManager $manager)
    {
        $this->shouldThrow('InvalidArgumentException')
            ->during('create', array('blog', $manager))
        ;
    }

    public function it_should_throw_exception_on_resource_without_model(ORMObjectManager $manager)
    {
        $this->shouldThrow('InvalidArgumentException')
            ->during('create', array('car', $manager))
        ;
    }
}
