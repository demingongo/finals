<?php

namespace Novice\Middleware\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Novice\Event\FilterControllerEvent;
use Novice\Event\FilterResponseEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

use Doctrine\Common\Annotations\Reader,
	Doctrine\Common\Util\ClassUtils;
	
use Novice\Annotation\ConfigurationInterface,
	Novice\Annotation\Assign;

class AssignControllerMiddleware implements EventSubscriberInterface
{
	
	protected $reader;
	
	protected $container;

    protected $defaultVarIndex = 0;


    public function __construct(Reader $reader, ContainerInterface $container)
    {
        $this->reader = $reader;
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

        foreach ($class->getMethods() as $method) {
			if ($method->getName() != $methodName){
            	$this->defaultVarIndex = 0;
				$annots = $this->reader->getMethodAnnotations($method);
				$annot = $this->getAssignAnnotation($annots, $class);
				if ($annot != null) {
                		$this->assign($annot, $class, $method, $objet);
            	}
			}
		}
        
		
        
    }
	
	protected function getDefaultVarName(\ReflectionObject $class, \ReflectionMethod $method)
    {
        $name = strtolower(str_replace('\\', '_', $class->name).'_'.$method->name);
        //if ($this->defaultVarIndex > 0) {
            $name .= '_'.$this->defaultVarIndex;
        //}
        ++$this->defaultVarIndex;

        return $name;
    }
	
	protected function assign($annot, \ReflectionObject $class, \ReflectionMethod $method, $objet)
    {
		
		$routeName = $this->container->get('request_stack')->getCurrentRequest()->attributes->get("_route");
		$assignRoutes = $annot->getRoutenames();

		if($routeName !== null && !empty($assignRoutes)){
			if(!in_array($routeName, $assignRoutes)){
				return;
			}
		}

        $name = $annot->getName();
        if (null === $name) {
            $name = $this->getDefaultVarName($class, $method);
        }
		
		if(!($this->container->get('templating')->getTemplateVars($name) === null)){
			// if varname existe deja dans le template engine
			return;
		}
			
		$args = array();
        foreach ($method->getParameters() as $param) {
			if(isset($args[$param->getName()])){
				throw new \LogicException(sprintf('Multiple parameters "%s" in "%s::%s".', $param->getName(), $class->getName(),$method->getName()));
			}
			if($param->getName() == "request"){
				$args["request"] = $this->container->get('request_stack')->getCurrentRequest();
			}
			/*else if($this->container->get('request_stack')->getCurrentRequest()->attributes->has($param->getName())){
				$args[] = $this->container->get('request_stack')->getCurrentRequest->attributes->get($param->getName());
			}*/
			else if($this->container->has($param->getName())){
				$args[$param->getName()] = $this->container->get($param->getName());
			}
            else if (/*!isset($defaults[$param->getName()]) && */$param->isDefaultValueAvailable()) {
                //$defaults[$param->getName()] = $param->getDefaultValue();
				$args[$param->getName()] = $param->getDefaultValue();
            }
			else{
				throw new \Exception("Cannot use annotation @Assign for METHOD with PARAMETERS with no default value,
				 except if parameter name is 'request' or a service Id");
			}
        }
		
		$var = $method->invokeArgs($objet, $args);
		
		$nocache = $annot->getNocache();

        if (null === $nocache) {
            $nocache = false;
        }
		
		/** todo **/
		$this->container->get('templating')->assign($name, $var, $nocache);   
		   
    }

    protected function getAssignAnnotation(array $annotations, \ReflectionObject $class)
    {
        $retour = null;
        foreach ($annotations as $annot) {
            if ($annot instanceof Assign) {
                if ($retour == null) {
                    $retour = $annot;
                } else {
                    throw new \LogicException(sprintf('Multiple "%s" annotations are not allowed in "%s".', get_class($annot), $class->getName()));
                }
            }
        }

        return $retour;
    }

	public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => array('onController', -120),
        );
    }
}
