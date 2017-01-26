<?php
namespace Novice\Form;

use Novice\Entity\Entity;
use Symfony\Component\DependencyInjection\ContainerAwareTrait,
	Symfony\Component\DependencyInjection\ContainerAwareInterface,
	Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Novice\Form\Extension\ExtensionInterface;

abstract class FormBuilder implements ContainerAwareInterface
{
  protected $form;
  //protected $extensions;

  /**
   * @inherit
   */
  protected $container;
  
  public function __construct(Entity $entity)
  {
	$instance = (string) $this->getEntityInstance();

	if(!empty($instance) && !$entity instanceof $instance){
		throw new \InvalidArgumentException(sprintf("Argument 1 passed to ".__METHOD__." must be an instance of %s, instance of %s given",$instance,get_class($entity)));
	}
	
	$this->setForm(new Form($entity));
  }

  /**
   * @inherit
   */
  public function setContainer(ContainerInterface $container = null)
  {
      $this->container = $container;
	  return $this;
  }
  
  public function setForm(Form $form)
  {
    $this->form = $form;
	$this->getForm()->setName($this->getName());
  }
  
  public function form()
  {
    return $this->getForm();
  }
  
  public function getForm()
  {
    return $this->form;
  }

  public function add($field)
  {
     $this->form->add($field);
	 return $this;
  }

  public function getEntityInstance()
  {
     return '';
  }

  public function getName()
  {
     return 'form';
  }

  /*public function addEventListener($eventName, $listener, $priority = 0)
  {
     $this->form->dispatcher()->addListener($eventName, $listener, $priority);
  }

  public function addEventSubscriber(EventSubscriberInterface $subscriber)
  {
     $this->form->dispatcher()->addSubscriber($subscriber);
  }*/

  public function addExtension(ExtensionInterface $extension)
  {
	$this->form()->addExtension($extension);
	
	return $this;
  }

  abstract public function build();
}