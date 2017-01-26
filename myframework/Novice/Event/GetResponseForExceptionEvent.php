<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novice\Event;

use Symfony\Component\HttpFoundation\Request;

class GetResponseForExceptionEvent extends GetResponseEvent
{
    /**
     * The exception object
     * @var \Exception
     */
    private $exception;

    public function __construct($app, Request $request, \Exception $e)
    {
        parent::__construct($app, $request);

        $this->setException($e);
    }

    /**
     * Returns the thrown exception
     *
     * @return \Exception  The thrown exception
     *
     * @api
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Replaces the thrown exception
     *
     * This exception will be thrown if no response is set in the event.
     *
     * @param \Exception $exception The thrown exception
     *
     * @api
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
