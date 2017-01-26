<?php

namespace Novice\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PostResponseEvent extends Event
{
    private $app;

    private $request;

    private $response;

    public function __construct($app, Request $request, Response $response)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
