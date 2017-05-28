<?php

namespace DoctrineModule\Form\Extension\EntityNode;

use Novice\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Event\Event,
	Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\FormEvents;

use DoctrineExtensions\NestedSet;

use Doctrine\ORM\QueryBuilder;

class EntityNodeExtension extends \Novice\Form\Extension\Extension implements EventSubscriberInterface
{
	private $nsm;
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

	const PARADOX = 410041;

	public function __construct(NestedSet\Manager $nsm,  array $options = array()){

		$this->nsm = $nsm;

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
			FormEvents::EXECUTE => array(
                array('onFormExecute', 0),
            ),
        );
    }

	public function onFormRequest(FilterRequestEvent $event)
	{
		$request = $event->getRequest();
		$form = $event->getForm();
		$entity = $form->entity();
		$em = $this->getEntityManager();

		$rootField = $this->nsm->getConfiguration()->getRootFieldName();
		$lftField = $this->nsm->getConfiguration()->getLeftFieldName();

		$value = null;
		
		if($request->isMethod('POST')){
			$postBag = $request->request;
			if($postBag->has($form->getName())){
				$formValues = $postBag->get($form->getName());

				if(isset($formValues[$this->name])){
					$value = $formValues[$this->name];
					if(get_class($event->getForm()->entity()) != $this->nsm->getConfiguration()->getClassname())
					{
						$foreignEntity = $em->find($this->entity_class, $value);
						if(!empty($foreignEntity)){
							$entity[$this->name] = $foreignEntity;
						}
					}
				}
			}
		}
		
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

	public function onFormExecute(FilterFormEvent $event)
	{
		if(get_class($event->getForm()->entity()) != $this->nsm->getConfiguration()->getClassname())
		{return;}

		$em = $this->getEntityManager();
		$form = $event->getForm();
		$entity = $form->entity();
		$id = $entity->getId();

		$node = $em->find($this->entity_class, $form->getField($this->name)->value());
		
		//dump($node);
		//exit(__METHOD__);

		$em->getConnection()->beginTransaction();
		try {		
			if($id === null){
				//if new entity

				if($node === null){
					$this->nsm->createRoot($entity);
				}
				else{
					$parent = $this->nsm->wrapNode($node);
					$entityW = $this->nsm->wrapNode($entity);
					//dump($parent);
					//exit(__METHOD__);
					$parent->addChild($entityW);
				}
			}
			else{
				// if it's an update

				if($node === null){
					//if no parent has been chosen
					/*$entityN = $this->nsm->wrapNode($entity);

					// TODO : if it wasn't already a root node, move it as root, otherwise do nothing
					if(!$entityN->isRoot()){

					}*/
				}
				else{
					$parent = $this->nsm->wrapNode($node);
					$entityN = $this->nsm->wrapNode($entity);
					
					//if it doesn't try to be its own parent
					if(!$entityN->isEqualTo($node)) //($id != $node->getId())
					{
						$move = true;
						if($entityN->hasParent()){
							$p = $entityN->getParent();
							$move = ($parent->isEqualTo($p)) ? false : $move;//($p->getId() == $node->getId()) ? false : $move;
						}
					
						if($move){						
							if($parent->isDescendantOf($entity)){
								throw new \InvalidArgumentException(get_class($entity)." object cannot be its own descendant", self::PARADOX);
							}
							$entityN->moveAsLastChildOf($parent); //it also moves its decendants ...
						}
						/*else{
							$em->persist($entity);
							$em->flush();
						}*/
					}
				}
			}
			$em->persist($entity);
			$em->flush();
			$em->getConnection()->commit();
			$this->create($event);
			$form->reSetEntityValues();
		}
		catch (\Exception $e) {
			$em->close();
			$em->getConnection()->rollback();
			throw $e;
		}
	}

	public function create(Event $event)
	{
		$array = $this->options;

		$array['name']=$this->name;
		$array['multiple']=false;

		$em = $this->getEntityManager();
		$repository = $em->getRepository($this->entity_class);
		//$meta = $em->getClassMetadata($this->entity_class);
		//$identifier = $meta->getSingleIdentifierFieldName();

		//$method = 'get'.$identifier;

		if($this->query_builder != null && $this->query_builder instanceof QueryBuilder){
			$this->nsm->getConfiguration()->setBaseQueryBuilder($this->query_builder);
		}

		if(!empty($this->choice_label))
			$c_l = true;
		else
			$c_l = false;

		$rootField = $this->nsm->getConfiguration()->getRootFieldName();
		
		$rootNodes = $em->createQueryBuilder()->select('n')
			->from($this->entity_class, 'n')
			->where('n.'.$rootField.' = n.id')
			->orderBy('n.name')
			->getQuery()
			->getResult();
		//dump($rootNodes);
		//exit();
		$trees = array();

		/*foreach($rootNodes as $rootNode){
			$trees = array_merge($trees,$this->nsm->fetchTreeAsArray($rootNode->getId()));
		}*/
		//trees is an array of DoctrineExtensions\NestedSet\NodeWrapper objects

		/*foreach ($trees as $no) {
			echo str_repeat('-', $no->getLevel()) . $no . "<br>";
		}*/
		$treeForOpt = array();
		if($c_l){
			if(is_string($this->choice_label)){
				foreach($rootNodes as $rootNode){
					$ch_lab = $rootNode[$this->choice_label];
					$treeForOpt[$ch_lab] = $this->nsm->fetchTreeAsArray($rootNode->getId());
				}
			}
			else{
				$callable = $this->choice_label;
				foreach($rootNodes as $rootNode){
					$ch_lab = $callable($rootNode);
					$treeForOpt[$ch_lab] = $this->nsm->fetchTreeAsArray($rootNode->getId());
				}
			}
		}
		else{
			foreach($rootNodes as $rootNode){
				$ch_lab = (string) $rootNode;
				$treeForOpt[$ch_lab] = $this->nsm->fetchTreeAsArray($rootNode->getId());
			}
		}

		
		$array['optgroups'] = array();
		$array['options'] = array();
		/*if($c_l){
			if(is_string($this->choice_label))
			{
				foreach($trees as $fE){
					$array['options'][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).' '.$fE->getNode()[$this->choice_label];
				}
			}
			else{
				$callable = $this->choice_label;
				foreach($trees as $fE){
					$array['options'][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).' '.$callable($fE->getNode());
				}
			}
		}
		else{
			foreach($trees as $fE){
				$array['options'][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).' '.$fE;
			}
		}*/

		if($c_l){
			if(is_string($this->choice_label))
			{
				foreach($treeForOpt as $key => $value){
					$array['optgroups'][$key] = array();
					foreach($value as $fE){
					$array['optgroups'][$key][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).'	'.$fE->getNode()[$this->choice_label];
					}
				}
			}
			else{
				$callable = $this->choice_label;
				foreach($treeForOpt as $key => $value){
					$array['optgroups'][$key] = array();
					foreach($value as $fE){
					$array['optgroups'][$key][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).'	'.$callable($fE->getNode());
					}
				}
			}
		}
		else{
			foreach($treeForOpt as $key => $value){
				$array['optgroups'][$key] = array();
				foreach($value as $fE){
				$array['optgroups'][$key][$fE->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;', $fE->getLevel()).str_repeat('|_', $fE->getLevel()).'	'.$fE;
				}
			}
		}
		//dump($array['options']);
		//exit(__METHOD__);
		

		$class = new \ReflectionClass($this->element_type);
		$field = $class->newInstanceArgs(array($array));
		
		//dump($field);
		//exit(__METHOD__);

		$event->getForm()->add($field);

		$entity = $event->getForm()->entity();
		//$foreignEntity = $entity[$this->name];
		/*$idValue = $entity->getId();
		$rootValue = $entity->getRootValue();
		$leftValue = $entity->getLeftValue();*/
		
		if(get_class($event->getForm()->entity()) == $this->nsm->getConfiguration()->getClassname()){
			$node = $this->nsm->wrapNode($entity);
			if($node->hasParent()){
				$parent = $node->getParent()->getNode();
				$field->setValue($parent->getId());
			}
		}
		else{
			$foreignEntity = $entity[$this->name];
			if(!empty($foreignEntity))
				$field->setValue($entity[$array['name']]->getId());
		}

		$this->created = true;
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
		/*if(empty($this->entityManager)){
			$this->entityManager = $this->nsm->getManager($this->em);
		}*/
		return $this->nsm->getEntityManager();
	}
}