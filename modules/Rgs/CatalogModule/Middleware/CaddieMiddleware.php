<?php

namespace Rgs\CatalogModule\Middleware;

use Novice\Event\GetResponseEvent;
use Novice\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
	Symfony\Component\EventDispatcher\EventDispatcherInterface,
	Symfony\Component\HttpFoundation\RedirectResponse;

class CaddieMiddleware implements EventSubscriberInterface
{

	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		//do something to control authentication
		
		$container = $dispatcher->getContainer();
		
		$container->get('rgs.caddie')->update();
		
	}
	
	public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST => array(array('onRequest', 0, '^/')),
        );
    }
}
