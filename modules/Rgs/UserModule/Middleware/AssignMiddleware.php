<?php

namespace Rgs\UserModule\Middleware;

use Novice\Event\GetResponseEvent,
	Novice\Event\FilterResponseEvent;

use Novice\Form\Extension\Csrf\CsrfExtension;

use Symfony\Component\Security\Core\Util\SecureRandom,
	Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Rgs\UserModule\Form\LoginFormBuilder,
	Rgs\UserModule\Entity\User;

class AssignMiddleware
{
	public function onResponse(FilterResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		$container = $dispatcher->getContainer();
		if(!$container->get('session')->isAuthenticated()){

			$generator = new SecureRandom();
			$csrfExtension = new CsrfExtension($container->get('session'), /*$generator->nextBytes(32)*/ "login_modal", 25*60);

			$formBuilder = new LoginFormBuilder(new User());
			$formBuilder->setContainer($container)
						->build();
			$form = $formBuilder->addExtension($csrfExtension)
								->form();
			
			$container->get('templating')->assign('formModalLogin', $form->createView());

		}
		//exit(__METHOD__);
		$container->get('templating')->assign(array('session' => $container->get('session')));
		$container->get('templating')->assign(array('session_flash' => $container->get('session')->getFlashBag()));
		$container->get('templating')->assign(array('app' => 
			array(
			'user' => $container->get('app.user'),
			)
		));

		$event->getResponse()->prepare($event->getRequest());
		$event->getResponse()->setCharset('UTF-8');
	}
}
