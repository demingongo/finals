<?php

namespace Novice\Middleware\EventSubscriber;

use Novice\Middleware\Middleware;
use Novice\Event\FilterControllerEvent;
use Novice\Event\FilterResponseEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

use Doctrine\Common\Annotations\Reader,
	Doctrine\Common\Util\ClassUtils;
	
use Novice\Annotation\ConfigurationInterface;

class ControllerMiddleware implements EventSubscriberInterface
{
	
	protected $reader;


    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

	public function onController(FilterControllerEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		if (!is_array($controller = $event->getController())) {
            return;
        }

        $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);
		
		$object = new \ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $classConfigurations = $this->getConfigurations($this->reader->getClassAnnotations($object));
        $methodConfigurations = $this->getConfigurations($this->reader->getMethodAnnotations($method));

        $configurations = array();
        foreach (array_merge(array_keys($classConfigurations), array_keys($methodConfigurations)) as $key) {
            if (!array_key_exists($key, $classConfigurations)) {
                $configurations[$key] = $methodConfigurations[$key];
            } elseif (!array_key_exists($key, $methodConfigurations)) {
                $configurations[$key] = $classConfigurations[$key];
            } else {
                if (is_array($classConfigurations[$key])) {
                    if (!is_array($methodConfigurations[$key])) {
                        throw new \UnexpectedValueException('Configurations should both be an array or both not be an array');
                    }
                    $configurations[$key] = array_merge($classConfigurations[$key], $methodConfigurations[$key]);
                } else {
                    // method configuration overrides class configuration
                    $configurations[$key] = $methodConfigurations[$key];
                }
            }
        }

        $request = $event->getRequest();
        foreach ($configurations as $key => $attributes) {
            $request->attributes->set($key, $attributes);
        }
    }

    protected function getConfigurations(array $annotations)
    {
        $configurations = array();
        foreach ($annotations as $configuration) {
            if ($configuration instanceof ConfigurationInterface) {
                if ($configuration->allowArray()) {
                    $configurations['_'.$configuration->getAliasName()][] = $configuration;
                } elseif (!isset($configurations['_'.$configuration->getAliasName()])) {
                    $configurations['_'.$configuration->getAliasName()] = $configuration;
                } else {
                    throw new \LogicException(sprintf('Multiple "%s" annotations are not allowed.', $configuration->getAliasName()));
                }
            }
        }

        return $configurations;
    }

	public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => 'onController',
        );
    }
}
