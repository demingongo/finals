<?php

namespace Novice\Form\Extension\Csrf;

use Novice\Form\Form;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Util\SecureRandom,
	Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Field\InputField;

use Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\Event\GetValidEvent,
	Novice\Form\FormEvents;

class CsrfExtension extends \Novice\Form\Extension\Extension implements EventSubscriberInterface
{ 

  private static $tokens = array();

  protected $secret;
  protected $session;

  protected $tokenExpires;

  protected $fieldName = '_csrf_token';

  protected $value;
  
  public function __construct(Session $session , $secret = null, $tokenExpires = 900)
  {
	if(empty($secret)){
		// generate a CSRF secret
		$generator = new SecureRandom();
		$secret = $generator->nextBytes(32);//bin2hex($generator->nextBytes(16));
	}
	
	$this->secret=$secret;
	$this->tokenExpires = (int) $tokenExpires;
	$this->session=$session;
  }

   public static function getSubscribedEvents()
    {
        return array(
			FormEvents::REQUEST => array(
                array('onFormRequest', 0),
            ),
			FormEvents::IS_VALID => array(
                array('onFormIsValid', 100),
            ),
            FormEvents::CREATE_VIEW => array(
                array('onFormCreateView', 0),
            ),
        );
    }

  protected function getSessionId()
  {
        $this->session->start();

        return $this->session->getId();
  }

  public function generateCsrfToken()
  {
	  	//dump("GENERATE TOKEN");
        return sha1($this->secret.$this->getSessionId());
  }

  public function isCsrfTokenValid($ttl = 900 /* 15*60 */)
  {
		$_retour = false;
		$session = $this->session;
		$name = $this->form->getName().'_'.$this->getSessionId();
		if($session->has($name.'_token') && $session->has($name.'_token_time'))
		{
			//Si le jeton de la session correspond à celui du formulaire
			if(StringUtils::equals($session->get($name.'_token'), $this->value))
			{
				//Si le jeton n'est pas expiré
				if($session->get($name.'_token_time') >= (time() - $ttl))
				{
					//if(StringUtils::equals($request->server->get('HTTP_REFERER'),$referer))
					//{
						$_retour = true;
					//}
				}
			}
		}

		return $_retour;
  }

  public function handleRequestBag(\Symfony\Component\HttpFoundation\ParameterBag $requestBag)
  {
	  if($requestBag->has($this->form->getName())){
		$formVal = $requestBag->get($this->form->getName());
		//dump($formVal);
		$this->value = $formVal[$this->fieldName];
	  }
  }

  public function handleRequest(\Symfony\Component\HttpFoundation\Request $request)
  {
	  if($request->isMethod('POST')){
		$this->handleRequestBag($request->request);
	  }
  }

  public function onFormRequest(FilterRequestEvent $event)
  {
	  $requestBag = $event->getRequest()->request;
	  if($requestBag->has($event->getForm()->getName())){
		$formVal = $requestBag->get($event->getForm()->getName());
		if(isset($formVal[$this->fieldName])){
			$this->value = $formVal[$this->fieldName];
		}
	  }
  }

  public function onFormIsValid(GetValidEvent $event)//$ttl = 900 /* 15*60 */)
  {
		$_valid = false;

		$session = $this->session;
		$name = $event->getForm()->getName().'_'.$this->getSessionId();
		if($session->has($name.'_token') && $session->has($name.'_token_time'))
		{
			//dump("il ya un jeton dans la session");
			/*dump($name.'_token');
			dump($session->get($name.'_token'));
			dump($this->value);*/
			
			//Si le jeton de la session correspond à celui du formulaire
			if(StringUtils::equals($session->get($name.'_token'), $this->value))
			{
				//dump("le jeton de la session correspond à celui du formulaire");
				/*dump($this->value);
				dump($name.'_token');
				dump($session->get($name.'_token'));
				dump(StringUtils::equals($session->get($name.'_token'), $this->value));
				exit("Equal : ".__METHOD__);*/
				
				//Si le jeton n'est pas expiré
				if($session->get($name.'_token_time') >= (time() - $this->tokenExpires))
				{
					//if(StringUtils::equals($request->server->get('HTTP_REFERER'),$referer))
					//{
						$_valid = true;
					//}
				}
			}
			/*else{
				dump($this->value);
				dump($name.'_token');
				dump($session->get($name.'_token'));
				dump(StringUtils::equals($session->get($name.'_token'), $this->value));
				//exit("NOT equal : ".__METHOD__);
				}*/
		}

		$event->setValid($_valid);
		
		/*dump($_valid);
		exit(__METHOD__);*/
		
		//$this->session->remove($name.'_token');
		//$this->session->remove($name.'_token_time');
		if($event->getValid()){
			$this->session->remove($name.'_token');
			$this->session->remove($name.'_token_time');
		}
		else{
			throw new CsrfSecurityException('Unauthorized command', CsrfSecurityException::SECURITY_EXCEPTION);
		}
  }

  public function onFormCreateView(FilterFormEvent $event)
  {
	$csrfSecret = "";
	//$name = $event->getForm()->getName();
	$name = $event->getForm()->getName().'_'.$this->getSessionId();

	$tokenName = $name.'_token';
	$tokenTimeName = $name.'_token_time';

	if(array_key_exists( $tokenName , self::$tokens ))
	{
		$csrfSecret = self::$tokens[$tokenName];
	}
	else{
		$csrfSecret = $this->generateCsrfToken();
		self::$tokens[$tokenName] = $csrfSecret;
	}
	
	$this->saveTokenInSession($csrfSecret, $tokenName, $tokenTimeName);

	$field = new InputField(array(
		'type' => 'hidden',
		'name' => $this->fieldName,
		'value'=> $csrfSecret,
		/*'validators' => array(
    new CsrfTokenValidator($this->session, $tokenName, $tokenTimeName, 15*60),),*/
		));
	$event->getForm()->add($field);
	
  }

  private function saveTokenInSession($csrfSecret, $tokenName, $tokenTimeName)
  {
    $session = $this->session;
	$session->set($tokenName, $csrfSecret);
	$session->set($tokenTimeName, time());
	
	//dump("SAVE: ".$session->get($tokenName));
	//dump(__METHOD__);
  }

  public function clearCsrfToken()
  {
	//$name = $this->form->getName();
	$name = $this->form->getName().'_'.$this->getSessionId();

	$tokenName = $name.'_token';
	$tokenTimeName = $name.'_token_time';

	$this->session->remove($tokenName);
	$this->session->remove($tokenTimeName);
  }
}