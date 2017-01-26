<?php
namespace Novice\Middleware;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareMiddlewareDispatcher extends MiddlewareDispatcher
{
	/**
     * The container from where services are loaded.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * The service IDs of the event listeners and subscribers.
     *
     * @var array
     */
    private $listenerIds = array();

	/**
     * The services registered as listeners.
     *
     * @var array
     */
    private $listeners = array();


	/**
     * Constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

	public function addListenerService($eventName, $callback, $priority = 0, $pattern = '^/')
    {
        if (!is_array($callback) || 2 !== count($callback)) {
            throw new \InvalidArgumentException('Expected an array("service", "method") argument');
        }

        $this->listenerIds[$eventName][] = array($callback[0], $callback[1], $priority, $pattern);
    }

	public function removeListener($eventName, $listener)
    {
        $this->lazyLoad($eventName);

        if (isset($this->listenerIds[$eventName])) {
            foreach ($this->listenerIds[$eventName] as $i => $args) {
                list($serviceId, $method, $priority) = $args;
                $key = $serviceId.'.'.$method;
                if (isset($this->listeners[$eventName][$key]) && $listener === array($this->listeners[$eventName][$key], $method)) {
                    unset($this->listeners[$eventName][$key]);
                    if (empty($this->listeners[$eventName])) {
                        unset($this->listeners[$eventName]);
                    }
                    unset($this->listenerIds[$eventName][$i]);
                    if (empty($this->listenerIds[$eventName])) {
                        unset($this->listenerIds[$eventName]);
                    }
                }
            }
        }

        parent::removeListener($eventName, $listener);
    }

    public function hasListeners($eventName = null)
    {
        if (null === $eventName) {
            return (bool) count($this->listenerIds) || (bool) count($this->listeners);
        }

        if (isset($this->listenerIds[$eventName])) {
            return true;
        }

        return parent::hasListeners($eventName);
    }

    public function getListeners($eventName = null)
    {
        if (null === $eventName) {
            foreach ($this->listenerIds as $serviceEventName => $args) {
                $this->lazyLoad($serviceEventName);
            }
        } else {
            $this->lazyLoad($eventName);
        }

        return parent::getListeners($eventName);
    }

	/**
     * Adds a service as event subscriber.
     *
     * @param string $serviceId The service ID of the subscriber service
     * @param string $class     The service's class name (which must implement EventSubscriberInterface)
     */
    public function addSubscriberService($serviceId, $class)
    {
        foreach ($class::getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->listenerIds[$eventName][] = array($serviceId, $params, 0);
            } elseif (is_string($params[0])) {
                $this->listenerIds[$eventName][] = array($serviceId, $params[0], isset($params[1]) ? $params[1] : 0, isset($params[2]) ? $params[2] : '^/');
            } else {
                foreach ($params as $listener) {
                    $this->listenerIds[$eventName][] = array($serviceId, $listener[0], isset($listener[1]) ? $listener[1] : 0, isset($listener[2]) ? $listener[2] : '^/');
                }
            }
        }
    }


	/**
     * {@inheritdoc}
     *
     * Lazily loads listeners for this event from the dependency injection
     * container.
     *
     * @throws \InvalidArgumentException if the service is not defined
     */
    public function dispatch($eventName, BaseEvent $event = null)
    {
        $this->lazyLoad($eventName);

        return parent::dispatch($eventName, $event);
    }

	public function dispatchMiddlewares($eventName, Event $event = null)
    {
        $this->lazyLoad($eventName);

        return parent::dispatchMiddlewares($eventName, $event);
    }

	public function getContainer()
    {
        return $this->container;
    }


    /**
     * Lazily loads listeners for this event from the dependency injection
     * container.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     */
    protected function lazyLoad($eventName)
    {
        if (isset($this->listenerIds[$eventName])) {
            foreach ($this->listenerIds[$eventName] as $args) {
                list($serviceId, $method, $priority, $pattern) = $args;
                $listener = $this->container->get($serviceId);

                $key = $serviceId.'.'.$method;
                if (!isset($this->listeners[$eventName][$key])) {
					//dump($key);
                    $this->addListener($eventName, array($listener, $method), $priority, $pattern);
                } elseif ($listener !== $this->listeners[$eventName][$key]) {
                    parent::removeListener($eventName, array($this->listeners[$eventName][$key], $method));
                    $this->addListener($eventName, array($listener, $method), $priority, $pattern);
                }
				//exit('-lazyLoad-');

                $this->listeners[$eventName][$key] = $listener;
            }
        }
    }
 
}
