<?php

namespace Rgs\AdminModule\Middleware;

use Novice\Event\GetResponseEvent;
use Novice\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
	Symfony\Component\EventDispatcher\EventDispatcherInterface,
	Symfony\Component\HttpFoundation\RedirectResponse;

class ContentManagerMiddleware implements EventSubscriberInterface
{

    private $defaultController;

    private $defaultTemplate;

    public function __construct($defaultController, $defaultTemplate){
        $this->defaultController = $defaultController;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function onRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher){
		$request = $event->getRequest();
		$attributes = $request->attributes->all();
		
        if(!isset($attributes['content_manager']))
            return;
        
        if(!isset($attributes['_controller']))
            $request->attributes->set('_controller', $this->defaultController);
        if(!isset($attributes['_template']))
            $request->attributes->set('_template', $this->defaultTemplate);
	}
	
	public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST => array(array('onRequest', 0, '^/admin/(articles|categories|brands|states|advertisements)')),
        );
    }


}