<?php

namespace Novice\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent
{
    private $app;

    private $request;

    public function __construct($app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
