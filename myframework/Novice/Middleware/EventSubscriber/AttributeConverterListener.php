<?php

namespace Novice\Middleware\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Novice\Event\FilterControllerEvent;
use Novice\Event\GetResponseForControllerResultEvent;
use Novice\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Novice\Annotation\AttributeConverter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Annotation\Editor\PropertyEditorRegistry;
 

class AttributeConverterListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
	
	/**
     * @var PropertyEditorRegistry
     */
	protected $editorRegistry;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container instance
	 * @param PropertyEditorRegistry $editorRegistry
     */
    public function __construct(ContainerInterface $container, PropertyEditorRegistry $editorRegistry)
    {
        $this->container = $container;
		$this->editorRegistry = $editorRegistry;
    }

    /**
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onController(FilterControllerEvent $event, $eventname, EventDispatcherInterface $dispatcher)
    {
        $request = $event->getRequest();
        $ac = $request->attributes->get('_attribute_converter');

        // no @AttributeConverter present
        if (null === $ac) {
            return;
        }
		
		if(!is_array($ac)){
			throw new \InvalidArgumentException('Request attribute "_attribute_converter" is reserved for @AttributeConverter annotations.');
		}
		
		$attributes = array();
		
		foreach($ac as $annot){
			
			if($annot->getName() === null){
				continue;
			}

        	// we need the @AttributeConverter annotation object or we cannot continue
	        if (!$annot instanceof AttributeConverter) {
	            throw new \InvalidArgumentException('Request attribute "_attribute_converter" is reserved for @AttributeConverter annotations.');
	        }

			$attributes[$annot->getName()] = $this->buildAttribute($event->getController(), $request, $annot);
		}
		
		foreach($attributes as $k => $v){
			$request->attributes->set($k, $v);
		}
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => array('onController', -128),
        );
    }
	

    /**
     * @param Request  $request
     * @param Template $template
     * @param object   $controller
     * @param string   $action
     *
     * @return array
     */
    private function resolveDefaultParameters(Request $request, Template $template, $controller, $action)
    {
        $parameters = array();
        $arguments = $template->getVars();

        if (0 === count($arguments)) {
            $r = new \ReflectionObject($controller);

            $arguments = array();
            foreach ($r->getMethod($action)->getParameters() as $param) {
                $arguments[] = $param->getName();
            }
        }
		
        // fetch the arguments of @Template.vars or everything if desired
        // and assign them to the designated template
        foreach ($arguments as $argument) {
            $parameters[$argument] = $request->attributes->get($argument);
        }

        return $parameters;
    }
	
	private function buildAttribute($controller, Request $request, AttributeConverter $annot)
    {		
		if (!is_array($controller)) {
            return;
        }
		
		list($controller, $method) = $controller;
		
		$class = new \ReflectionClass($annot->getClass());
		$instance = $class->newInstance();
		$from = $annot->getFrom();
		$editorClass = null;
		$editor = $annot->getEditor();
		/*if (!(null === $editor = $annot->getEditor())){
			$editorClass = new \ReflectionClass($editor);
			if(!in_array("Novice\Annotation\Editor\PropertyEditorInterface", $editorClass->getInterfaceNames())){
				throw new \InvalidArgumentException(
					sprintf('Class "%s" must implement "Novice\Annotation\Editor\PropertyEditorInterface"',$editorClass->getName())
				);
			}
			$editorClass->newInstanceArgs(array($this->container));
		}*/
		
		
		if(!($from === null)){
			
			$properties = array();
			
			if($from == AttributeConverter::REQUEST){
				$properties = $request->request->all();
			}
			else if($from == AttributeConverter::QUERY){
				$properties = $request->query->all();
			}
			else if($from == AttributeConverter::ATTRIBUTES){
				$properties = $request->attributes->all();
			}
			else if($from == AttributeConverter::FORMDATA){
				$properties = $request->request->all();
				$properties += $request->files->all();
			}
			
			$prefix = $annot->getPrefix();
			if($prefix === null){
				foreach($properties as $name => $value){
					if(!$class->hasProperty($name)){
						continue;
					}
					
					if( ($editor === null) ||
						(!($editor === null) && !$this->editorRegistry->edit($editor, $request, $name, $value, $instance, $class))){
						$method = "set".ucfirst($name);
						if($class->hasMethod($method) && $class->getMethod($method)->getNumberOfRequiredParameters() <= 1){					
							$instance->$method($value);
						}
					}
				}
			}
			else{
				foreach($properties as $name => $value){
					if (!(0 === strpos($name, $prefix))){
						//if it doesn't start by the prefix
						continue;
					}
					
					$name = substr($name, strlen($prefix));
					
					if(!$class->hasProperty($name)){
						continue;
					}
					
					if( ($editor === null) ||
						(!($editor === null) && !$this->editorRegistry->edit($editor, $request, $name, $value, $instance, $class))){
						$method = "set".ucfirst($name);
						if($class->hasMethod($method) && $class->getMethod($method)->getNumberOfRequiredParameters() <= 1){					
							$instance->$method($value);
						}
					}
				}
			}
			
		}
		
		return $instance;
		
		exit(__METHOD__);
		
		

		if(is_array($controller) && is_object($controller[0])){
			$controllerInstance = $controller[0];
			$method = $controller[1];
			$tplFile = $method;
			$module = "";
			if($controllerInstance instanceof \Novice\BackController){
				$module = $controllerInstance->getModule();
			
				$pos = strpos($method, 'execute');
				if($pos === 0){
					$method = substr($method, 7);
					$tplFile = strtolower(substr($method, 0, 1)) . substr($method, 1);
				}
			}
			$tplFile .= '.tpl';
			
			if(null !== $module && !empty($module)){
				$contentFile = 'file:['.$module.']'.$tplFile;				
			}
			else{
				$contentFile = 'file:'.$tplFile;
			}
			
			$this->container->get('templating')->setContentFile($contentFile);
		}
	}
}
