<?php

namespace Novice\Form\Extension\Securimage;

use Novice\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Extension\Securimage\Field\SecurimageField,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator,
	Novice\Form\Validator\NotNullValidator;

use Novice\Form\Event\Event,
	Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\FormEvents;

class SecurimageExtension extends \Novice\Form\Extension\Extension implements EventSubscriberInterface
{ 

  protected $fieldName = '_captcha_code';

  protected $nullMessage;

  protected $errorMessage;

  private $created = false;
  
  public function __construct($nullMessage = null, $errorMessage = null)
  {
	  $this->setNullMessage($nullMessage);
	  $this->setErrorMessage($errorMessage);
  }

   public static function getSubscribedEvents()
    {
        return array(
			FormEvents::REQUEST => array(
                array('onFormRequest', 0),
            ),
			FormEvents::CREATE_VIEW => array(
                array('onFormCreateView', 0),
            ),
        );
    }

  public function setNullMessage($nullMessage)
  {
	 $this->nullMessage = $nullMessage;
  }

  public function setErrorMessage($errorMessage)
  {
	$this->errorMessage = $errorMessage;
  }

  public function getNullMessage()
  {
	 return $this->nullMessage;
  }

  public function getErrorMessage()
  {
	return $this->errorMessage;
  }

  public function onFormRequest(FilterRequestEvent $event)
  {
	if(!$this->created){
		$this->create($event);
	}
  }

  public function onFormCreateView(FilterFormEvent $event)
  {
	if(!$this->created){
		$this->create($event);
	}
  }

  private function create(Event $event)
  {
	if($this->nullMessage == null){
		$this->nullMessage = 'Enter the code to continue';
	}
	
	if($this->errorMessage == null){
		$this->errorMessage = 'The security code entered was incorrect';
	}

	$namespace = $event->getForm()->getName();

	$field = new SecurimageField(array(
		'label' => 'Security Verification',
		'name' => $this->fieldName,
		'input_text' => 'Enter the security code:',
		//'show_audio_button' => false,
		'namespace'=> $namespace,
		'control_label' => true,
		'validators' => array(
			new NotNullValidator($this->nullMessage),
			new SecurimageValidator($this->errorMessage, $namespace),
		)));
	$event->getForm()->add($field);

	$this->created = true;
  }
}