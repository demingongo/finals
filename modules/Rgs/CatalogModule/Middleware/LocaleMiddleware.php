<?php

namespace Rgs\CatalogModule\Middleware;

use Novice\Middleware\Middleware;
use Novice\Event\GetResponseEvent;
use Novice\Event\FilterResponseEvent;
use Novice\Password;

use Rgs\UserModule\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Novice\Events;

class LocaleMiddleware implements EventSubscriberInterface
{
	 private $defaultLocale;

	 public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
		
    }

	public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
			//$request->setLocale($this->defaultLocale);
            return;
        }

		if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
			//$request->getSession()->set('_locale', $request->getLocale());
			//dump($request);
        }

		$smarty = $dispatcher->getContainer()->get('templating');
		
		if(!$smarty->debugging)
			$smarty->configLoad('file:lang/lang.conf', $request->getLocale());

		//$request->setLocale($request->getSession()->get('_locale', 'fr'));
		/*dump($request->getLocale());
		exit(__METHOD__);*/
	}

	public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            Events::REQUEST => array(array('onRequest', 31, '^/')),
        );
    }
}
