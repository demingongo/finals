<?php

namespace Rgs\UserModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\StreamedErrorResponse;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Rgs\UserModule\Entity\User;

class AdminAppMiddleware
{
	private function isAdmin(EventDispatcherInterface $dispatcher){
		$container = $dispatcher->getContainer();
		if($container->get('session')->isAuthenticated()){
			$user = $container->get('app.user')->getData();
			//allow access if user has role ROLE_ADMIN
			if(!empty($user) && $user instanceof User && $user->hasRole(User::ROLE_ADMIN)){
				return true;
			}
		}	
		return false;
	}
	
	public function onNotFound(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$container = $dispatcher->getContainer();
		// if not auth, 403 forbidden
		if(!$container->get('session')->isAuthenticated()){
			$event->setResponse(new StreamedErrorResponse($dispatcher->getContainer()->get('templating'), 403));
		}
	}
	
	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		// is login route
		$isLoginRoute = $event->getRequest()->attributes->get('_route') == 'rgs_admin_login';
		
		$container = $dispatcher->getContainer();
		// if not auth and is login route
		if(!$container->get('session')->isAuthenticated() && $isLoginRoute){
			return;
		}
		
		// if not admin, 403 forbidden
		if(!$this->isAdmin($dispatcher)){
			$event->setResponse(new StreamedErrorResponse($dispatcher->getContainer()->get('templating'), 403));
		}
	}
}
