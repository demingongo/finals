<?php
namespace Novice\Form\Field;

class SelectField extends Field
{
  protected $autofocus = false;
  protected $disabled = false;
  protected $form;
  protected $multiple = false;
  protected $size;
  protected $class;
  protected $empty_option = true;
  protected $empty_option_value = '';
  protected $empty_option_text = '';
  protected $options = array();

  protected $optgroups = array();

  public function setValue($value)
  {
    if (is_array($value))
    {
      $this->value = $value;
    }
	else
	{
	  parent::setValue($value);
	}

	return $this;
  }

  public function setEmpty_option($bool)
  {
      $this->empty_option = (bool)$bool;

	return $this;
  }

  public function setEmpty_option_value($value)
  {
    if (is_string($value) || is_numeric($value) || is_bool($value))
    {
      $this->empty_option_value = $value;
    }

	return $this;
  }

  public function setEmpty_option_text($text)
  {
    if (is_string($text) || is_numeric($text))
    {
      $this->empty_option_text = $text;
    }

	return $this;
  }
  
  public function buildWidget()
  {
    $widget = '<div class="';

	if($this->feedback){

	if (!empty($this->errorMessage))
	{
			$widget .= 'has-error';
	}
	else if (!empty($this->warningMessage))
	{
			$widget .= 'has-warning';
	}
	else if (empty($this->errorMessage) && !empty($this->value))
	{
			$widget .= 'has-success';
	}

	}

	$widget .= '">';

	$widget .= '<div>'; //think of inline in case that's what we want
	
	$widget .= $this->buildLabelTag();

	$widget .= $this->buildMessage();
	
	$widget .= '</div>';

	$widget .= $this->buildSelectTag();
    
    return $widget .= '</div>';
  }

  public function setAutofocus($autofocus)
  {
    $this->autofocus = (bool) $autofocus;
  }

  public function setDisabled($disabled)
  {
    $this->disabled = (bool) $disabled;
  }

  public function setMultiple($multiple)
  {
    $this->multiple = (bool) $multiple;
  }

  public function getMultiple()
  {
    return (bool) $this->multiple;
  }

  public function setForm($form)
  {
    $this->form = (string) $form;
  }

  public function setSize($size)
  {
    $this->size = (int) $size;
  }
  
  public function setOptions(array $options)
  {
    foreach ($options as $k => $v)
    {
      if (is_string($v) && (is_string($k) || is_numeric($k)))
      {
        $this->options[$k] = $v;
      }
	  else
	  {
      throw new \RuntimeException('Le texte de l\'option doit être une chaine de caracteres');
	  }
    }
  }

  public function setOptgroups(array $optgroups)
  {
    foreach ($optgroups as $key => $value)
    {
      if (is_array($value) && (is_string($key) || is_numeric($key)))
      {
		foreach ($value as $k => $v)
		{
		  if (is_string($v) && (is_string($k) || is_numeric($k)))
		  {
	      }
		  else
		  {
			throw new \RuntimeException('"optgroups": Le texte de l\'option doit être une chaine de caracteres');
		  }
	    }
        $this->optgroups[$key] = $value;
      }
	  else
	  {
      throw new \RuntimeException('Le valeurs de "optgroups" doivent des arrays d\'options');
	  }
    }
  }

  public function setClass($class)
  {
    $this->class = (string) $class;
  }

  protected function buildSelectTag()
  {
	$widget = '<select id="'.$this->getId().'" name="'.$this->getFullName();

	if($this->multiple){
		$widget .= '[]';
	}
	
	$widget .= '"';

	$widget .= ' class="';

	if($this->bootstrap){
		$widget .= 'form-control';
	}

	if(!empty($this->class)){
		$widget .= ' '.$this->class;
	}

	$widget .= '"';

	if($this->autofocus){
		$widget .= ' autofocus';
	}

	if($this->disabled){
		$widget .= ' disabled';
	}

	if(!empty($this->form)){
		$widget .= ' form="'.$this->form.'"';
	}

	if($this->multiple){
		$widget .= ' multiple';
	}

	if($this->required){
		$widget .= ' required';
	}

	if(!empty($this->size)){
		$widget .= ' size="'.$this->size.'"';
	}

	if (!empty($this->attributes))
    {
		foreach($this->attributes as $attr => $val)
			{
				$widget .= ' '.$attr.'="'.$val.'"';
			}
    }


	$widget .= '>';

	$widget .= $this->buildOptionTag();

	$widget .= $this->buildOptgroupTag();

	return $widget .= '</select>';
  }

  protected function buildOptionTag()
  {
	  $options = '';

	  $options .= $this->buildEmptyValueOptions();

	  if(empty($this->options)){
		  return $options;
	  }
		
	  $postValue = &$this->value; // dump($postValue); exit();
	  
	  if(is_array($this->value)){
		  $closure = function($value) use ($postValue){
				return in_array($value, $postValue, false);
		  };
	  }
	  else{
		  $closure = function($value) use ($postValue){
				return $value == $postValue;
		  };
	  }
		
	  foreach($this->options as $k => $v)
	  {
		$options .= '<option value="'.htmlspecialchars($k).'"';

		if ($closure($k))
		{
			$options .= ' selected';
		}

		$options .= ' >';
	
		$options .=$v;
	
		$options .= '</option>';
	  }

	  return $options;
  }

  protected function buildOptgroupTag()
  {
	  $optgroups = '';

	  if(empty($this->optgroups)){
		  return $optgroups;
	  }
		
	  $postValue = &$this->value; // dump($postValue); exit();
	  
	  if(is_array($this->value)){
		  $closure = function($value) use ($postValue){
				return in_array($value, $postValue, false);
		  };
	  }
	  else{
		  $closure = function($value) use ($postValue){
				return $value == $postValue;
		  };
	  }

	  foreach($this->optgroups as $key => $value)
	  {
		$optgroups .= '<optgroup label="'.htmlspecialchars($key).'">';

		foreach($value as $k => $v){
			$optgroups .= '<option value="'.htmlspecialchars($k).'"';

			if ($closure($k))
			{
				$optgroups .= ' selected';
			}

			$optgroups .= ' >';
	
			$optgroups .=$v;
	
			$optgroups .= '</option>';
		}

		$optgroups .= '</optgroup>';
	  }

	  return $optgroups;
  }

  protected function buildEmptyValueOptions()
  {
	  if(!$this->multiple && $this->empty_option){
		return '<option label="empty" value="'.$this->empty_option_value.'">'.$this->empty_option_text.'</option>';
	  }
  }
}