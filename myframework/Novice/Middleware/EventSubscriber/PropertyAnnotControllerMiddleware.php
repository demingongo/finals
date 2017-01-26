<?php

namespace Novice\Middleware\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Novice\Event\FilterControllerEvent;
use Novice\Event\FilterResponseEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

use Doctrine\Common\Annotations\Reader,
	Doctrine\Common\Util\ClassUtils;
	
use Novice\Annotation\ConfigurationInterface,
	Novice\Annotation\Service;
	
use Doctrine\Common\Annotations\IndexedReader;

class PropertyAnnotControllerMiddleware implements EventSubscriberInterface
{
	
	protected $reader;
	
	protected $container;


    public function __construct(Reader $reader, ContainerInterface $container)
    {
        $this->reader = new IndexedReader($reader);
		$this->container = $container;
    }

	public function onController(FilterControllerEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		if (!is_array($controller = $event->getController())) {
            return;
        }
		
		$objet = $controller[0];
		$methodName = $controller[1];
		
		if (!is_object($objet)) {
			return;
        }

        $class = new \ReflectionObject($objet);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        foreach ($class->getProperties() as $property) {
				$annot = $this->reader->getPropertyAnnotation($property, "Novice\Annotation\Service");
				if ($annot != null) {
                	$service = $this->getService($annot, $property, $class);
					if($property->isPrivate() || $property->isProtected()){
						$property->setAccessible(true);
						$property->setValue($objet, $service);
						$property->setAccessible(false);
					}
					elseif($property->isStatic()){
						$property->setValue($service);
					}
					else{
						$property->setValue($objet, $service);
					}
            	}
		}
        
		
        
    }
	
	protected function getService(\Novice\Annotation\Service $annot, \ReflectionProperty $property, \ReflectionClass $class)
    {
		$id = $annot->getValue();
		if(empty($id)){
			$id = $property->getName();
		}
		
		try{
			return $this->container->get($id);	
		}
		catch(ServiceNotFoundException $ex){
			throw new \Exception(sprintf('Annotation @Service for property "%s" in "%s": 
			%s', $property->getName(), $class->getName(), $ex->getMessage()));
		}
    }

	public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => array('onController', -24),
        );
    }
}
