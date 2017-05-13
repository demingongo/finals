<?php

namespace Rgs\CatalogModule\Middleware;

use Novice\Event\FilterResponseEvent,
	Novice\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
	Symfony\Component\EventDispatcher\EventDispatcherInterface;


class RgsAssignMiddleware implements EventSubscriberInterface
{
	public function onResponse(FilterResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$container = $dispatcher->getContainer();
		
		$container->get('templating')->assign(array('rgs' => 
			array(
			'caddie' => $container->get('rgs.caddie'),
			)
		));
	}
	
	public static function getSubscribedEvents()
    {
        return array(
            Events::RESPONSE => array(array('onResponse', 0, '^/')),
        );
    }
}
