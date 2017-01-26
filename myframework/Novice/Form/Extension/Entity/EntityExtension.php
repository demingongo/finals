<?php

namespace Novice\Form\Extension\Entity;

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

use Doctrine\ORM\QueryBuilder;

class EntityExtension extends \Novice\Form\Extension\Extension implements EventSubscriberInterface
{
	const SELECT_FIELD = 'Novice\Form\Field\SelectField';
	const RADIO_FIELD = 'Novice\Form\Field\RadioField';
	const CHECKBOX_FIELD = 'Novice\Form\Field\CheckboxField';

	private $doctrine;
	private $entity_class;
	private $choice_label;
	private $group_by;
	private $query_builder;
	private $element_type = 'Novice\Form\Field\SelectField';
	private $em;
	private $entityManager;
	private $name;

	private $created = false;

	protected $options = array();

	public function __construct(\DoctrineModule\ManagerRegistry $doctrine,  array $options = array()){

		$this->doctrine = $doctrine;

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
			else
			{
				$this->options[$type] = $value;
			}
		}

		return $this;
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
		$request = $event->getRequest();
		$form = $event->getForm();
		$entity = $form->entity();
		$em = $this->getEntityManager();

		$value = null;
		
		if($request->isMethod('POST')){
			$postBag = $request->request;
			if($postBag->has($form->getName())){
				$formValues = $postBag->get($form->getName());

				if(isset($formValues[$this->name])){
					$value = $formValues[$this->name];
					if(!is_array($value)){
						$foreignEntity = $em->find($this->entity_class, $value);
						/*dump($value);
						dump($foreignEntity);
						exit(__METHOD__);*/
						if(!empty($foreignEntity))
							$entity[$this->name] = $foreignEntity;
					}
					else{
						//$arrayCollection = new \Doctrine\Common\Collections\ArrayCollection();
						//dump($value);
						//$arrayCollection = $entity[$this->name];
						$className = $this->getEntityManager()->getClassMetadata($this->entity_class)->getName();
						$className = substr($className, strrpos($className, "\\")+1);
						$method = "add".ucfirst($className);
						foreach($value as $id){
							$foreignEntity = $em->find($this->entity_class, $id);
							if(!empty($foreignEntity)){
								//$arrayCollection[] = $foreignEntity;
								$entity->$method($foreignEntity);
							}
						}
						//$entity[$this->name] = $arrayCollection;
						/*dump($entity);
						exit(__METHOD__);*/
					}
				}
				else
				{
					$multi = false;
					if(isset($this->options['multiple'])){
						$multi = (bool) $this->options['multiple'];
					}
					if($this->element_type == self::CHECKBOX_FIELD || ($this->element_type == self::SELECT_FIELD && $multi)){
						$arrayCollection = $entity[$this->name];
						$className = $this->getEntityManager()->getClassMetadata(get_class($entity))->getName();
						$className = substr($className, strrpos($className, "\\")+1);
						$method = "set".ucfirst($className);
						
						/*foreach($arrayCollection as $a){
							dump($a);
							$arrayCollection->removeElement($a);
							$a->$method(null);
							dump($a);
						}*/
						//dump($this->getEntityManager()->getClassMetadata(get_class($entity)));
						//exit(__METHOD__);

						/*dump($entity);
						dump('nope');
						exit(__METHOD__);*/
					}
				}
			}
		}

		//$rep = $em->getRepository($this->entity_class);
		
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

	public function create(Event $event)
	{
		$array = $this->options;

		$array['name']=$this->name;

		$em = $this->getEntityManager();
		//$repository = $em->getRepository($this->entity_class);
		$meta = $em->getClassMetadata($this->entity_class);
		$identifier = $meta->getSingleIdentifierFieldName();

		//$method = 'get'.$identifier;

		/*if($this->query_builder != null){
			$qb = $this->query_builder;
			if($this->query_builder instanceof QueryBuilder)
				$foreignEntities = $qb->getQuery()->execute();
			else
				$foreignEntities = $qb($repository)->getQuery()->execute();
		}
		else{
			$foreignEntities = $repository->findBy(array(),array(),null,null);
		}

		if(!empty($this->choice_label))
			$c_l = true;
		else
			$c_l = false;
		
		$array['options'] = array();
		if($c_l){
			if(is_string($this->choice_label))
			{
				foreach($foreignEntities as $fE){
					$array['options'][$fE[$identifier]] = $fE[$this->choice_label];
				}
			}
			else{
				$callable = $this->choice_label;
				foreach($foreignEntities as $fE){
					$array['options'][$fE[$identifier]] = $callable($fE);
				}
			}
		}
		else{
			foreach($foreignEntities as $fE){
				$array['options'][$fE[$identifier]] = $fE;
			}
		}
		

		$class = new \ReflectionClass($this->element_type);
		$field = $class->newInstanceArgs(array($array));*/

		$field = $this->createField();
		
		//dump($field);
		//exit(__METHOD__);

		$event->getForm()->add($field);

		$entity = $event->getForm()->entity();
		$foreignEntity = $entity[$this->name];
		
		if($field instanceof \Novice\Form\Field\CheckboxField || ($field instanceof \Novice\Form\Field\SelectField && $field->getMultiple() == true)){
			$arrayValue = array();
			foreach($entity[$array['name']] as $fE){
				$arrayValue[] = $fE[$identifier];
			}
			$field->setValue($arrayValue);
			//dump($arrayValue);
			//dump($field->value());
			//exit(__METHOD__);
		}
		else{
			if(!empty($foreignEntity))
				$field->setValue($entity[$array['name']][$identifier]);
		}

		$this->created = true;
	}

	public function createField(){
		$array = $this->options;

		$array['name']=$this->name;

		$em = $this->getEntityManager();
		$repository = $em->getRepository($this->entity_class);
		$meta = $em->getClassMetadata($this->entity_class);
		$identifier = $meta->getSingleIdentifierFieldName();

		if($this->query_builder != null){
			$qb = $this->query_builder;
			if($this->query_builder instanceof QueryBuilder)
				$foreignEntities = $qb->getQuery()->execute();
			else
				$foreignEntities = $qb($repository)->getQuery()->execute();
		}
		else{
			$foreignEntities = $repository->findBy(array(),array(),null,null);
		}

		if(!empty($this->choice_label))
			$c_l = true;
		else
			$c_l = false;
		
		$array['options'] = array();
		if($c_l){
			if(is_string($this->choice_label))
			{
				foreach($foreignEntities as $fE){
					$array['options'][$fE[$identifier]] = $fE[$this->choice_label];
				}
			}
			else{
				$callable = $this->choice_label;
				foreach($foreignEntities as $fE){
					$array['options'][$fE[$identifier]] = $callable($fE);
				}
			}
		}
		else{
			foreach($foreignEntities as $fE){
				$array['options'][$fE[$identifier]] = $fE;
			}
		}
		

		$class = new \ReflectionClass($this->element_type);
		return $field = $class->newInstanceArgs(array($array));
	}

	public function setClass($string){
		return $this->setEntity_class($string);
	}

	public function setEntity_class($string){
		if(is_string($string) && !empty($string)){
			$this->entity_class = $string;
		}
		return $this;
	}

	public function setChoice_label($choice_label){
		if((is_string($choice_label) && !empty($choice_label)) || is_callable($choice_label)){
			$this->choice_label = $choice_label;
		}
		return $this;
	}

	public function setQuery_builder($query_builder){
		if($query_builder instanceof QueryBuilder || is_callable($query_builder)){
			$this->query_builder = $query_builder;
		}
		return $this;
	}

	public function setElement_type($string){
		if(is_string($string) && !empty($string)){
			switch($string){
				case 'SELECT_FIELD':
					$this->element_type = $this::SELECT_FIELD;
					break;
				case 'RADIO_FIELD':
					$this->element_type = $this::RADIO_FIELD;
					break;
				case 'CHECKBOX_FIELD':
					$this->element_type = $this::CHECKBOX_FIELD;
					break;
				default:
					$this->element_type = $string;
					break;
			}
		}
		return $this;
	}

	public function setEm($string){
		if(is_string($string)){
			$this->em = $string;
		}
		return $this;
	}

	public function setName($string){
		if(is_string($string) && !empty($string)){
			$this->name = $string;
		}
		return $this;
	}

	public function getEntityManager(){
		if(empty($this->entityManager)){
			$this->entityManager = $this->doctrine->getManager($this->em);
		}
		return $this->entityManager;
	}
}