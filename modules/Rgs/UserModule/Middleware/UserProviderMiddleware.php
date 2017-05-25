<?php

namespace Rgs\UserModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Password;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Rgs\UserModule\Entity\User;

class UserProviderMiddleware
{

	protected $container;

	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		//do something to control authentication
		
		$this->container = $dispatcher->getContainer();

		$usm = new \Novice\User\UserSessionManager($this->container->get('session'));

		if($usm->isAuthenticated()){
			$this->retrieveById($usm->getId(), $event);
		}
		else if($usm->retrieveLoginCookie($event->getRequest())->hasCookie()){
			$selector = $usm->getCookieId();
			$token = $usm->getCookieToken();
			$this->retrieveByToken($selector, $token, $event);
		}
	}

	public function retrieveById($id){
		$usm = new \Novice\User\UserSessionManager($this->container->get('session'));
		$em = $this->container->get('managers')->getManager();
		$user = $em->getRepository('UserModule:User')->findOneById($id);
		if(null != $user){
			if($user->isLocked()){
				return $usm->logout();
			}
			$this->container->get('app.user')->setData($user);
			
		}
		else if($usm->isAuthenticated() && $user == null){
			$usm->setAuthenticated(false);
		}
	}

	public function retrieveByToken($id, $token, $event){
		if($dbToken = $this->container->get('managers')->getManager()->getRepository('UserModule:AuthToken')->findOneById($id)){
			if(Password::verify($token, $dbToken->getToken())){
				$usm = new \Novice\User\UserSessionManager($this->container->get('session'));
				
				if($dbToken->getUser()->isLocked()){
					$em = $this->container->get('managers')->getManager();
					$em->getConnection()->beginTransaction();
					try{
						$em->remove($dbToken);
						$em->flush();
						$em->getConnection()->commit();
					}
					catch(\Exception $e){
						$em->close();
						$em->getConnection()->rollback();
					}
					$response = new RedirectResponse($event->getRequest()->getUriForPath('/'));
		 			$response = $response->send();
					$usm->clearLoginCookie($response);
					$usm->logout();
					$event->setResponse($response);
					return;
				}

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
