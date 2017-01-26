<?php

namespace Rgs\UserModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Password;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserProviderMiddleware
{

	protected $container;

	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		//do something to control authentication
		
		$this->container = $dispatcher->getContainer();

		$usm = new \Novice\User\UserSessionManager($this->container->get('session'));

		if($usm->isAuthenticated()){
			$this->retrieveById($usm->getId());
		}
		else if($usm->retrieveLoginCookie($event->getRequest())->hasCookie()){
			$selector = $usm->getCookieId();
			$token = $usm->getCookieToken();
			$this->retrieveByToken($selector, $token);
		}
	}

	public function retrieveById($id){
		$usm = new \Novice\User\UserSessionManager($this->container->get('session'));
		$user = $this->container->get('managers')->getManager()->getRepository('UserModule:User')->findOneById($id);
		if(null != $user){
			$this->container->get('app.user')->setData($user);
		}
		else if($usm->isAuthenticated() && $user == null){
			$usm->setAuthenticated(false);
		}
	}

	public function retrieveByToken($id, $token){
		if($dbToken = $this->container->get('managers')->getManager()->getRepository('UserModule:AuthToken')->findOneById($id)){
			if(Password::verify($token, $dbToken->getToken())){
				$usm = new \Novice\User\UserSessionManager($this->container->get('session'));
				$usm->login($dbToken->getUser()->getId());
				$this->container->get('app.user')->setData($dbToken->getUser());
				$dbToken->getUser()->setLastLogin(new \DateTime('now'));
				$em = $this->container->get('managers')->getManager();
				$em->persist($dbToken->getUser());
				$em->flush();
			}
		}
	}
}
