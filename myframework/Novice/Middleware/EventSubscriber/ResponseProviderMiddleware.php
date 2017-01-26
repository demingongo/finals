<?php

namespace Novice\Middleware\EventSubscriber;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseForControllerResultEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

class ResponseProviderMiddleware implements EventSubscriberInterface
{

	private $guessedContentFile;

	public function onController(FilterControllerEvent $event, $eventname, EventDispatcherInterface $dispatcher)
    {
		$this->guessedContentFile = $this->guessTemplate($event->getController());
    }

	public function onView(GetResponseForControllerResultEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		if($event->getResponse() !== null){
			return;
		}

		$templating = $dispatcher->getContainer()->get('templating');

		$result = $event->getControllerResult();
		
		if($templating->getContentFile() == null){
			if(!(null == $result) && is_string($result)){
				$templating->setContentFile($result);
			}
			else{
				$templating->setContentFile($this->guessedContentFile);
			}
		}
		
		
		$response = new \Symfony\Component\HttpFoundation\StreamedResponse(null, 200, array());
		$response->setCallback(array($templating, "getGeneratedPage"));
		$event->setResponse($response);
	
	}

	private function guessTemplate($controller)
    {		
		if (!is_array($controller)) {
            return;
        }

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
			
			return $contentFile;
		}
	}

	public static function getSubscribedEvents()
    {
        return array(
			Events::CONTROLLER => 'onController',
            Events::VIEW => array('onView', 0, '^/'),
        );
    }
}
