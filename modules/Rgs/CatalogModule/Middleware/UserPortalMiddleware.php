<?php

namespace Rgs\CatalogModule\Middleware;

use Novice\Event\GetResponseEvent;
use Novice\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
	Symfony\Component\EventDispatcher\EventDispatcherInterface,
	Symfony\Component\HttpFoundation\RedirectResponse,
	Symfony\Component\Security\Core\Util\StringUtils;

class UserPortalMiddleware implements EventSubscriberInterface
{

	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		//do something to control authentication
		
		$container = $dispatcher->getContainer();
		
		if(!$container->get('session')->isAuthenticated()){
			$request = $event->getRequest();
			if( $request->headers->has('referer') && !StringUtils::equals($request->headers->get('referer'), $request->getUri())){
				$login_redirect = $request->headers->get('referer');
			}
			else{
				$login_redirect = $request->getRequestUri();
			}
			
			$event->setResponse(new RedirectResponse($container->get('router')->generate('user_security_login', 
								array("redirect" =>  $login_redirect))));
			$container->get('session')->set('security_login_redirect', $login_redirect);
		}
		
	}
	
	public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            Events::REQUEST => array(array('onRequest', 0, '^/user')),
        );
    }
}
