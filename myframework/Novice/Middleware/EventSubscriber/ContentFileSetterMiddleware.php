<?php

namespace Novice\Middleware\EventSubscriber;

use Novice\Middleware\Middleware;
use Novice\Event\FilterControllerEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Password;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

class ContentFileSetterMiddleware //implements EventSubscriberInterface
{

	public function onController(FilterControllerEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$controller = $event->getController();
		
		if (!is_array($controller)) {
            return;
        }
		
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
			
			if(null !== $module){
				$contentFile = 'file:['.$module.']'.$tplFile;
				
			}
			else{
				$contentFile = 'file:'.$tplFile;
			}
			
			$dispatcher->getContainer()->get('templating')->setContentFile($contentFile);
		}
	}

	/*public static function getSubscribedEvents()
    {
        return array(
            Events::CONTROLLER => array(array('onController', 31, '^/')),
        );
    }*/
}
