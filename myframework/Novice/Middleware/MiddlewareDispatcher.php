<?php
namespace Novice\Middleware;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class MiddlewareDispatcher /*extends EventDispatcher*/ implements EventDispatcherInterface
{
    /**
     * The services registered as listeners.
     *
     * @var array
     */
    private $listeners = array();
	private $sorted = array();


	/**
     * @see EventDispatcherInterface::dispatch()
     *
     * @api
     */
    public function dispatch($eventName, BaseEvent $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        $this->doDispatch($this->getListeners($eventName), $eventName, $event);

        return $event;
    }

	/**
     * @see EventDispatcherInterface::getListeners()
     */
	public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return array_filter($this->sorted);
    }

	/**
     * @see EventDispatcherInterface::hasListeners()
     */
    public function hasListeners($eventName = null)
    {
        return (bool) count($this->getListeners($eventName));
    }

	/**
     * @see EventDispatcherInterface::addListener()
     *
     * @api
     */
    public function addListener($eventName, $listener, $priority = 0, $pattern = '^/')
    {
        $this->listeners[$eventName][$priority][] = array($pattern, $listener);
        unset($this->sorted[$eventName]);
    }

	/**
     * @see EventDispatcherInterface::removeListener()
     */
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
			foreach($listeners as $key => $callable){
				if (false !== (array_search($listener, array($callable[1]), true))) {
					unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
				}
			}
        }
    }

	/**
     * @see EventDispatcherInterface::addSubscriber()
     *
     * @api
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0, isset($params[2]) ? $params[2] : '^/');
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0, isset($listener[2]) ? $listener[2] : '^/');
                }
            }
        }
    }

    /**
     * @see EventDispatcherInterface::removeSubscriber()
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

	/**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners.
     * @param string     $eventName The name of the event to dispatch.
     * @param Event      $event     The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, BaseEvent $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener[1], $event, $eventName, $this);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

	/**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
	private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }

	public function dispatchMiddlewares($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        $this->doDispatchAccordingToPattern($this->getListeners($eventName), $eventName, $event);

        return $event;
    }

	protected function doDispatchAccordingToPattern($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
			if(0 !== $this->match($listener[0], $event->getRequest()))
			{
				call_user_func($listener[1], $event, $eventName, $this);
				if ($event->isPropagationStopped()) {
					break;
				}
			}
        }
    }

	protected function match($pattern, Request $request)
	{
		return preg_match("`".$pattern."`", $request->getPathInfo(), $matches);
	}
 
}
