<?php

namespace Rgs\UserModule\Middleware;

use Novice\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Rgs\UserModule\Form\LoginFormBuilder,
	Rgs\UserModule\Entity\User;

class AssignMiddleware
{
	public function onResponse(FilterResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		
		$container = $dispatcher->getContainer();
		if(!$container->get('session')->isAuthenticated()){
			

			$formBuilder = new LoginFormBuilder(new User());
			$formBuilder->setContainer($container)
						->build();
						
			$csrfExtension = new \Novice\Form\Extension\Csrf\CsrfExtension($container->get('session'), "login_modal", 25*60);
			$formBuilder->addExtension($csrfExtension);
			
			$form = $formBuilder->form();
			
			$container->get('templating')->assign('formModalLogin', $form->createView());

		}

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
