<?php

namespace Rgs\CatalogModule\Middleware;

use Novice\Event\GetResponseForExceptionEvent;
use Novice\Events;

use Novice\StreamedErrorResponse;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
	Symfony\Component\EventDispatcher\EventDispatcherInterface,
	Symfony\Component\HttpFoundation\RedirectResponse;

class ExceptionMiddleware
{

	public function onException(GetResponseForExceptionEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		$container = $dispatcher->getContainer();
		
		$tpl = $container->get('templating');

		$tpl->assign('message', $event->getException()->getMessage());

		$event->setResponse(StreamedErrorResponse::createResponse($tpl, 500));
		
	}

}
