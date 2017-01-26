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
			/**
			 *
			 * Faire les vérifications (si le user est au moins gestionnaire)
			 *
			 * $_SESSION["RF"]["allow_acces"] = true;
			 * return;
			 *
			 */
			//dump($user);
			//exit(__METHOD__);
			if(!empty($user) && $user instanceof User && $user->hasRole(User::ROLE_SUPER_ADMIN)){
				$_SESSION["RF"]["allow_acces"] = true;
				return;
			}
		}
		
		//$_SESSION["RF"]["allow_acces"] = true;
		$_SESSION["RF"]["allow_acces"] = false;
	}
}
