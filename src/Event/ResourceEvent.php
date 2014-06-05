<?php

namespace Anh\DoctrineResource\Event;

use Symfony\Component\EventDispatcher\Event;

class ResourceEvent extends Event
{
    const PRE_CREATE = 'pre_create';
    const POST_CREATE = 'post_create';
    const PRE_UPDATE = 'pre_update';
    const POST_UPDATE = 'post_update';
    const PRE_DELETE = 'pre_delete';
    const POST_DELETE = 'post_delete';

    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
