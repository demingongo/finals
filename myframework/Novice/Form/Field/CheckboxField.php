<?php
namespace Novice\Form\Field;

class CheckboxField extends Field
{
  protected $buttons = array();
  
  /*
   * @var int
   */
  protected $inline;

  protected $control_label = false;
  /*
  protected $autofocus;
  protected $checked;
  protected $disabled = array();
  protected $readonly;
  */
  protected $required = array();


  public function setValue($value)//redefinition de la methode setValue de la classe parente Field pour accepter if is_bool($value)
  {
    if (is_array($value))
    {
      $this->value = $value;
    }
  }
  
  public function buildWidget()
  { 
	$end = '';
	$widget = '';

	if(is_bool($this->required)){
		if($this->required){
			$this->required = $this->buttons;
		}
		else{
			$this->required = array();
		}
	}

	$widget .= '<span class=" ';

	if (!empty($this->errorMessage))
	{
			$widget .= ' has-error';
	}
	else if (!empty($this->warningMessage))
	{
			$widget .= ' has-warning';
	}
	else if (empty($this->errorMessage) && isset($this->value))
	{
			$widget .= ' has-success';
	}

	$widget .= '">';

	$widget .= '<span>';

	$widget .= $this->buildLabelTag();

	$widget .= $this->buildMessage();
	
	$widget .= '</span>';

	$widget .= '</span>';

	if(empty($this->inline)){
		$widget .= $this->buildButtons();
	}
	else{
		$widget .= $this->buildButtonsInline();
	}
    
    return $widget .= $end;
  }
  
  public function setButtons(array $buttons)
  {
    foreach ($buttons as $k => $v)
    {
      if (is_string($k) && (is_string($v) || is_numeric($v) || is_bool($v)))
      {
		if(is_bool($v) && $v==false)
		 {$v=0;}
		else if(is_bool($v) && $v==true)
		 {$v=1;}
        
		$this->buttons[$k] = $v;
      }
	  else
	  {
      throw new \RuntimeException('Les keys de l\'array "buttons" doivent être de type string');
	  }
    }
  }

  public function setInline($inline)
  {
    $inline = (int) $inline;
	if($inline >= 0){
		$this->inline = $inline;
	  }
  }

  public function setRequired($required)
  {
	if(!is_array($required) && !is_bool($required)){
		$required = array($required);
	}
	
	$this->required = $required;
  }

  protected function buildButtons()
  {
	  $widget = '';
			  
	  $class = 'checkbox';

	  foreach($this->buttons as $name => $value)
	  {
		$isChecked = is_array($this->value) && in_array($value, $this->value);
	
		$widget .='<div class="'.$class.'"><label';

		$widget .='>';

		$widget .=  $this->buildInputCheckbox($value, $isChecked);
  	
		$widget .= $name.'</label></div>';
	  }
	  return $widget;
  }

  protected function buildButtonsInline()
  {
	  $widget = '';

	  $class = 'checkbox-inline';

	  foreach($this->buttons as $name => $value)
	  {
		$isChecked = is_array($this->value) && in_array($value, $this->value);
		
		$widget .='<label class="'.$class.'">';

		$widget .=  $this->buildInputCheckbox($value, $isChecked);
  	
		$widget .= $name.'</label>';
	  }

	  return $widget;
  }

  protected function buildInputCheckbox($value, $isChecked = false)
  {
	  $checkbox = '<input type="checkbox" name="'.$this->getFullName().'[]"';
    
	  $checkbox .= ' value="'.htmlspecialchars($value).'"';

    
    if ($isChecked)
    {
      $checkbox .= ' checked ';
    }

	if (in_array($value,$this->required,false))
    {
      $checkbox .= 'required ';
    }
	
	return $checkbox .= ' />'; //end of checkbox type
  }
}