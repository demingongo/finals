<?php

namespace Novice\Form\Extension\Filemanager;

use Novice\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Extension\Filemanager\Field\FilemanagerField;
	//Novice\Form\Extension\Securimage\Field\SecurimageField,
	//Novice\Form\Extension\Securimage\Validator\SecurimageValidator,
	//Novice\Form\Validator\NotNullValidator;

use Novice\Form\Event\Event,
	Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\FormEvents;

use Novice\Form\Validator;

class FilemanagerExtension extends \Novice\Form\Extension\Extension implements EventSubscriberInterface
{
	private $created = false;
	private $validators = array();

	private $akeys = array();

	protected $options = array(
		'label' => null,
		'control_label' => true,
		'name' => '_filemanager',
		'type' => null,
        'fldr' => null,
        'sort_by' => null,
        'descending' => null,
        'lang' => "fr_FR",
        'relative_url' => 1,
        'popup' => null,
        'text_open_btn' => null,
        'show_remove_btn' => null,
        'text_remove_btn' => null,
        'show_input' => null,
        'input_attributes' => array(),
        'base_url' => null,
	    'filemanager_path' => null,
	);

	public function __construct($filemanager_path,  array $options = array()){
		$this->options['filemanager_path'] = (string)$filemanager_path;
		if(substr($this->options['filemanager_path'], -1) == "/" && substr($this->options['filemanager_path'], -1) == "\\"){
			$this->options['filemanager_path'] .= substr($this->options['filemanager_path'], 0, -1);
		}

		if (!empty($options))
		{
			$this->hydrate($options);
		}
	}

	public function hydrate($options)
	{
		foreach ($options as $type => $value)
		{
			$method = 'set'.ucfirst($type);
      
			if (is_callable(array($this, $method)))
			{
				$this->$method($value);
			}
			else if(in_array($type, array_keys($this->options)))
			{
				$this->options[$type] = $value;
			}
		}
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

	public function onFormRequest(FilterRequestEvent $event)
	{
		if($this->options['base_url'] == null){
			$this->setBaseUrl($event->getRequest()->getBaseUrl());
		}
		if(!$this->created){
			$this->create($event);
		}
	}

	public function onFormCreateView(FilterFormEvent $event)
	{
		if(!$this->created){
			/*if($this->options['base_url'] == null){
				$this->setBaseUrl($event->getRequest()->getBaseUrl());
			}*/
			$this->create($event);
		}
	}

	public function create(Event $event)
	{
		$array = $this->options;

		if(!empty($this->validators)){
			$array['validators']=$this->validators;
		}

		if(!empty($this->akeys)){
			$array['akey']=$this->akeys[array_rand($this->akeys, 1)];
		}
		
		/*dump($array);
		exit(__METHOD__);*/

		$event->getForm()->add(new FilemanagerField($array));

		$this->created = true;
	}

	public function setBaseUrl($string){
		if(is_string($string) && !empty($string)){
			$this->options['base_url'] = $string;
		}
		return $this;
	}

	public function addValidator(Validator $v){
		$this->validators[] = $v;
		return $this;
	}

	public function addAkey($akey){
		$this->akeys[] = $akey;
		return $this;
	}

	public function setAkeys(Array $akeys){
		$this->akeys = $akeys;
		return $this;
	}
}