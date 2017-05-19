<?php

 /** edited for Novice **/

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Novice\Middleware\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Novice\Event\FilterControllerEvent;
use Novice\Event\GetResponseForControllerResultEvent;
use Novice\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Novice\Annotation\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Util\ClassUtils;

/**
 * Handles the Template annotation for actions.
 *
 * Depends on pre-processing of the ControllerListener.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
 

class TemplateListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Guesses the template name to render and its variables and adds them to
     * the request object.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onController(FilterControllerEvent $event, $eventname, EventDispatcherInterface $dispatcher)
    {
        $request = $event->getRequest();
        $template = $request->attributes->get('_template');

        // no @Template present
        if (null === $template) {
			//$this->guessTemplate($event->getController());
            return;
        }

        // we need the @Template annotation object or we cannot continue
        if (!$template instanceof Template) {
			
			//if it's not a string
			if(!is_string($template)){
				throw new \InvalidArgumentException('Request attribute "_template" is reserved for @Template annotations.');
			}
			return;
        }

        $template->setOwner($controller = $event->getController());

        // when no template has been given, try to resolve it based on the controller
        if (null === $template->getTemplate()) {
            //$guesser = $this->container->get('sensio_framework_extra.view.guesser');
            //$template->setTemplate($guesser->guessTemplateName($controller, $request, $template->getEngine()));
			$template->setTemplate($this->guessTemplate($event->getController()));
        }
    }

    /**
     * Renders the template and initializes a new response object with the
     * rendered template content.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        /* @var Template $template */
        $request = $event->getRequest();
        $template = $request->attributes->get('_template');

        if (null === $template) {
            return;
        }

		if(is_string($template)){
			$templating = $this->container->get('templating');
				if($templating->getContentFile() === null)
					$templating->setContentFile($template);
			return;
		}

        $parameters = $event->getControllerResult();
        $owner = $template->getOwner();
        list($controller, $action) = $owner;

        // when the annotation declares no default vars and the action returns
        // null, all action method arguments are used as default vars
        if (null === $parameters) {
            $parameters = $this->resolveDefaultParameters($request, $template, $controller, $action);
        }

        // attempt to render the actual response
        $templating = $this->container->get('templating');
		
		//dump($parameters); exit(__METHOD__);
		
		$templating->assign($parameters);
		
		if($templating->getContentFile() === null)
			$templating->setContentFile($template->getTemplate());

        /*if ($template->isStreamable()) {
            $callback = function () use ($templating, $template, $parameters) {
                return $templating->stream($template->getTemplate(), $parameters);
            };

            $event->setResponse(new StreamedResponse($callback));
        }*/

        // make sure the owner (controller+dependencies) is not cached or stored elsewhere
        $template->setOwner(array());

        //$event->setResponse($templating->renderResponse($template->getTemplate(), $parameters));
		
		$response = new StreamedResponse(array($templating, "getGeneratedPage"));
		$event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => array('onController', -128),
            Events::VIEW => 'onView',
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
	
	private function guessTemplate($controller)
    {		
		if (!is_array($controller)) {
            return;
        }
		
		//exit(__METHOD__);

		if(is_array($controller) && is_object($controller[0])){
			
			$controllerInstance = $controller[0];
			$method = $controller[1];
			$tplFile = $method;
			
			$className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controllerInstance) : get_class($controllerInstance);

	        $module = $this->getModuleForClass($className);
			
			if ($module) {
           		while ($moduleName = $module->getName()) {
                	if (null === $parentModuleName = $module->getParent()) {
                    	$moduleName = $module->getName();

	                    break;
    	            }

	                $modules = $this->container->get("app")->getModule($parentModuleName, false);
    	            $module = array_pop($modules);
        	    }
				$module = $module->getName();
			}
			else{
				$module = "";
			}
			//$module = "";
			if($controllerInstance instanceof \Novice\BackController){
				//$module = $controllerInstance->getModule();
			
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
			
			return $contentFile;
		}
	}
	
	private function getModuleForClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $modules = $this->container->get("app")->getModules();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($modules as $module) {
                if (0 === strpos($namespace, $module->getNamespace())) {
                    return $module;
				}
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);
    }
}
