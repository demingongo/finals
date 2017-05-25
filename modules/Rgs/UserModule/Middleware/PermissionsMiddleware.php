<?php

namespace Rgs\UserModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\StreamedErrorResponse;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Rgs\UserModule\Entity\User;

class PermissionsMiddleware
{	
	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		// is login route
		if(!$event->getRequest()->attributes->has('_permissions')){
			return;
		}

		$permissions = $event->getRequest()->attributes->get('_permissions');
		if(is_string($permissions)){
			$permissions = array($permissions);
		}

		$container = $dispatcher->getContainer();

		if($container->get('session')->isAuthenticated()){
			$user = $container->get('app.user')->getData();
			//allow access if user has one of the roles
			$hasPermission = false;
			foreach($permissions as $role){
				if($user->hasRole($role)){
					$hasPermission = true;
					break;
				}
			}

			if($hasPermission){
				return;
			}
		}
		
		$event->setResponse(new StreamedErrorResponse($dispatcher->getContainer()->get('templating'), 403));
	}
}
