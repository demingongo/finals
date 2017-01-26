<?php
namespace Novice\Form\Field;

use Novice\Form\Validator\Validator;

abstract class Field
{
  private $id;
  private $fullname;
  protected $errorMessage;
  protected $warningMessage;
  protected $infoMessage;
  protected $label;
  protected $control_label = false;
  protected $label_class;
  protected $name;
  protected $validators = array();
  protected $value;
  protected $title;
  protected $attributes = array();
  protected $required = false;
  protected $bootstrap = true;
  protected $feedback = true;

  private $valid;

  private $rendered = false;
  
  public function __construct(array $options = array())
  {
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
    }
  }

  public function isRendered()
  {
	  return $this->rendered;
  }

  public function setRendered()
  {
        $this->rendered = true;

        return $this;
  }

  public function renderField()
  {	
	  if($this->isRendered()){
		  return null;
	  }

	 return $this->setRendered()->buildWidget();
  }
  
  abstract public function buildWidget();
  
  public function isValid()
  {
	if(isset($this->valid)){
		return $this->valid;
	}

    foreach ($this->validators as $validator)
    {
      if (!$validator->isValid($this->value))
      {
        $this->errorMessage = $validator->errorMessage();
        return $this->valid = false;
      }
    }
    
    return $this->valid = true;
  }

  public function title()
  {
    return $this->title;
  }
  
  public function label()
  {
    return $this->label;
  }
  
  /*public function length()
  {
    return $this->length;
  }*/

  /*public function setInfoMessage($message=' ')
  {
    if($this->isValid())
    {
      $this->infoMessage = $message;
    }
  }*/

  public function setWarningMessage($message=' ')
  {
    if($this->isValid())
    {
      $this->warningMessage = $message;
    }
  }

  public function getId()
  {
	if(empty($this->id))
	{
		$this->id = $this->name();
	}
    return $this->id;
  }
  
  public function name()
  {
    return $this->name;
  }

  public function getFullName()
  {
    if(empty($this->fullname))
	{
		$this->fullname = $this->name();
	}
    return $this->fullname;
  }
  
  public function validators()
  {
    return $this->validators;
  }
  
  public function value()
  {
    return $this->value;
  }

  public function setBootstrap($bool)
  {
    $this->bootstrap = (bool)$bool;
  }

  public function getBootstrap()
  {
    return $this->bootstrap;
  }

  public function setFeedback($bool)
  {
    $this->feedback = (bool)$bool;
  }

  public function getFeedback()
  {
    return $this->feedback;
  }

  public function setTitle($title)
  {
    if (is_string($title))
    {
      $this->title = $title;
    }
  }
  
  public function setLabel($label)
  {
    if (is_string($label))
    {
      $this->label = $label;
    }
  }
  
  /*public function setLength($length)
  {
    $length = (int) $length;
    
    if ($length > 0)
    {
      $this->length = $length;
    }
  }*/

  public function setId($id)
  {
    if (!empty($id) && is_string($id))
    {
      $this->id = $id;
    }
  }
  
  public function setName($name)
  {
    if (is_string($name))
    {
      $this->name = $name;
    }
  }

  public function setFullName($name)
  {
    if (is_string($name))
    {
      $this->fullname = $name;
    }
  }
  
  public function setValidators(array $validators)
  {
    foreach ($validators as $validator)
    {
      if ($validator instanceof Validator && !in_array($validator, $this->validators))
      {
        $this->validators[] = $validator;
      }
	  else{
		  //throw new ...
	  }
    }
  }
  
  public function setValue($value)
  {
    if (is_string($value) || is_numeric($value))
    {
      $this->value = $value;
    }
	
	return $this;
  }

  public function setLabel_class($label_class)
  {
    $this->label_class = (string) $label_class;
  }

  public function setControl_label($control_label)
  {
    $this->control_label = (bool) $control_label;
  }

  public function setAttributes(array $attributes)
  {
	  $keys = array_keys($attributes);
	  
	  if(false !== ($key = array_search('id', $keys, true)))
	  {
		  unset($attributes[$keys[$key]]);
	  }

	  $keys = array_keys($attributes);

	  if(false !== ($key = array_search('name', $keys, true)))
	  {
		  unset($attributes[$keys[$key]]);
	  }

	  foreach ($attributes as $name => $value)
      {
		$method = 'set'.ucfirst($name);
      
		if (is_callable(array($this, $method)))
		{
			$this->$method($value);
		}
		else{
		  $this->attributes[$name] = $value;
	    }
	  }

	  /*if(false !== ($key = array_search('type', $keys, true))
		  || false !== ($key = array_search('name', $keys, true)) ){
		  throw new \RuntimeException('Attribute "'.$keys[$key].'" cannot be added in "'.__CLASS__.'".
		  Define "'.$keys[$key].'" as a key of its construct\'s array argument. ');
	  }else{
		  $this->attributes = $attributes;
	  }*/
  }

  public function setRequired($required)
  {
    $this->required = (bool) $required;
  }

  protected function buildLabelTag()
  {
	  $widget = '';
	  
	  if (!empty($this->label))
      {
      $widget .= '<label';
	  
	  if (!empty($this->title))
    	{
      		$widget .= ' title="'.htmlspecialchars($this->title).'"';
    	}
		else
		{
			$widget .= ' title="'.htmlspecialchars($this->label).'"';
		}

		if($this->control_label || !empty($this->label_class))
		{
			$widget .= ' class="';

			if ($this->control_label)
    		{
      			$widget .= 'control-label';
    		}

			if (!empty($this->label_class))
    		{
      			$widget .= ' '.$this->label_class;
    		}

			$widget .= '"';
		}

	  $widget .= '>'.$this->label.'</label>';
	  }

	  return $widget;
  }

  protected function buildMessage()
  {
	$widget = '';
	
	if (!empty($this->errorMessage))
	{
			$widget .= ' <small';
			if ($this->control_label)
    		{
      			$widget .= ' class="control-label"';
    		}
			$widget .= '>'.htmlspecialchars($this->errorMessage).'</small>';
	}
	else if($this->warningMessage !== null)
	{
		$widget .= ' <small';
			if ($this->control_label)
    		{
      			$widget .= ' class="control-label"';
    		}
		$widget .= '>'.htmlspecialchars($this->warningMessage).'</small>';
	}

	return $widget;
  }
}