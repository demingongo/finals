<?php


namespace Novice\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterResponseEvent extends \Novice\Middleware\Event
{

    private $response;

    public function __construct($app, Request $request, Response $response)
    {
        parent::__construct($app, $request);

        $this->setResponse($response);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
