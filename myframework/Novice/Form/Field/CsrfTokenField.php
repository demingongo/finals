<?php
namespace Novice\Form\Field;

class InputField extends Field
{
  protected $disabled; //disabled attribute will not work with <input type="hidden">

  protected $form;

  protected $readonly;

  protected $class;

  protected $session:

  //protected $formaction; //formaction attribute is used with type="submit" and type="image"
  //protected $formmethod; //formmethod attribute can be used with type="submit" and type="image"
  //protected $formnovalidate;

  public function __construct(\Symfony\Component\HttpFoundation\Session\Session $session, array $options = array())
  {
	$this->session = $session;

	$option['name']= '_csrf_token';

    parent::__construct($options);
  }
  
  public function buildWidget()
  { 
    return $widget = '<input type="hidden" id="_csrf_token" name="'.$this->getFullName().'" value="'.$csrf_token.'" />';
  }
}