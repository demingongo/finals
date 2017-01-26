<?php
namespace Novice\Form;

use Novice\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;
use Novice\Form\Extension\Extension;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\Event\GetValidEvent;

class Form
{
  private $name = 'form';
  protected $entity;
  protected $fields;
  protected $extensions;
  
  protected $dispatcher;
  
  public function __construct(Entity $entity)
  {
	$this->dispatcher = new EventDispatcher();
    $this->setEntity($entity);
  }
  
  public function add(Field\Field $field)
  {
    $name = $field->name(); // On récupère le nom du champ.
	
	//$method = "get".ucfirst($attr);
    //$field->setValue($this->entity->$method()); // On assigne la valeur correspondante au champ.

	$field->setValue($this->entity[$name]);
    
	//$field->setFullName($this->name.'['.$name.']');
    $this->fields[$name] = $field; // On ajoute le champ passé en argument à la liste des champs.
    return $this;
  }
  
  public function createView()
  {

	$event = new FilterFormEvent($this);
	$this->dispatcher->dispatch(FormEvents::CREATE_VIEW, $event);
	
	$formView = new FormView();
    
    // On génère un par un les champs du formulaire.
    foreach ($this->fields as $name => $field)
    {
	  $field->setFullName($this->name.'['.$name.']');
	  $field->setId($this->name.'_'.$name);
	  $formView->fields[$name] = $field;
    }
	return $formView;
  }
  
  public function isValid()
  {  	  
	//$valid = true;

	$event = new GetValidEvent($this);
	$valid = $this->dispatcher->dispatch(FormEvents::IS_VALID, $event)->getValid();

    // On vérifie que tous les champs soient valides.
    foreach ($this->fields as $field)
    {
      if (!$field->isValid() && $valid)
      {
        $valid = false;
      }
    }
    
    return $valid;
  }
  
  public function entity()
  {
    return $this->entity;
  }
  
  public function setEntity(Entity $entity)
  {
	/*$event = new FilterFormEvent($this, $entity);
	$event = $this->dispatcher->dispatch(FormEvents::PRE_SET_ENTITY, $event);*/

    $this->entity = $entity;//$event->getEntity();
  }

  public function handleRequest(Request $request)
  {
	if($request->isMethod('POST')){
		$event = new FilterRequestEvent($this, $request);
		if($this->dispatcher->dispatch(FormEvents::REQUEST, $event)->isPropagationStopped()){
			return;
		}
		$request = $event->getRequest();
		$postBag = $request->request;
		if($postBag->has($this->name)){
			$formValues = $postBag->get($this->name);
			foreach ($this->fields as $field)
			{
				if(isset($formValues[$field->name()])){
					$name = $field->name();
					$this->entity[$name] = $formValues[$name];
					//$field->setValue($this->entity[$name]);
					$field->setValue($formValues[$name]);
				}
				/*if($postBag->has($field->getFullName())){
					$name = $field->name();
					$this->entity[$name] = $postBag->get($name);
					//$field->setValue($this->entity[$name]);
					$field->setValue($postBag->get($name));
				}*/
			}
		}
	}
  }

  public function setName($name)
  {
    if(!empty($name) && is_string($name))
	{
		$this->name = $name;
	}
  }

  public function getName()
  {
     return $this->name;
  }

  public function getField($name)
  {
    if(isset($this->fields[$name]))
	{
		return $this->fields[$name];
	}
  }

  public function addExtension(Extension $extension)
  {
	$extension->setForm($this);

	if($extension instanceof \Symfony\Component\EventDispatcher\EventSubscriberInterface){
		$this->dispatcher->addSubscriber($extension);
	}

    $this->extensions[] = $extension;
  }

  /*public function dispatcher()
  {
    return $this->dispatcher;
  }*/
}