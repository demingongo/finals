<?php

namespace Novice\Event;

use Symfony\Component\HttpFoundation\Request;

class GetResponseForControllerResultEvent extends GetResponseEvent
{
    /**
     * The return value of the controller
     *
     * @var mixed
     */
    private $controllerResult;

    public function __construct($app, Request $request, $controllerResult)
    {
        parent::__construct($app, $request);

        $this->controllerResult = $controllerResult;
    }

    /**
     * Returns the return value of the controller.
     *
     * @return mixed The controller return value
     *
     * @api
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * Assigns the return value of the controller.
     *
     * @param mixed $controllerResult The controller return value
     *
     * @api
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
}
