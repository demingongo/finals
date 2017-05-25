<?php

namespace Rgs\UserModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Password;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Rgs\UserModule\Entity\User;

class RFMiddleware
{
	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$_SESSION['RF']["subfolder"]="";
		
		$container = $dispatcher->getContainer();
		if($container->get('session')->isAuthenticated()){
			$user = $container->get('app.user')->getData();
			//allow access if user has role ROLE_ADMIN
			if(!empty($user) && $user instanceof User && $user->hasRole(User::ROLE_ADMIN)){
				$_SESSION["RF"]["allow_acces"] = true;
				return;
			}
		}
		
		//by default, don't allow access
		$_SESSION["RF"]["allow_acces"] = false;
	}
}
