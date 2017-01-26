<?php
namespace Novice\Form\Field;

class RadioField extends Field
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
  protected $disabled;
  protected $readonly;
  */
  protected $required = false;


  public function setValue($value)//redefinition de la methode setValue de la classe parente Field pour accepter if is_bool($value)
  {
    if (is_string($value) || is_bool($value))
    {
      $this->value = $value;
    }
  }
  
  public function buildWidget()
  { 
	$end = '';
	$widget = '';
	if($this->required){
		$widget = '<div data-toggle="buttons"><div class="btn-group">';
		$end = '</div></div>';
	}

	$widget .= '<div class=" ';

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

	//$widget .= '<p>';

	$widget .= $this->buildLabelTag();

	$widget .= $this->buildMessage();
	
	//$widget .= '</p>';

	$widget .= '</div>';

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
    $this->required = (bool) $required;
  }

  protected function buildButtons()
  {
	  $widget = '';
	  $attr = array();
		
	  if($this->required)
		{
			$class = 'btn btn-default';
			$attr[] = 'class="sr-only" required';
		}
		else{
			$class = 'radio';
		}

	  foreach($this->buttons as $name => $value)
	  {
		$isChecked = ($value == $this->value) && isset($this->value);
		
		if($isChecked && $this->required)
			$cl = $class.' active';
		else
			$cl = $class;
	
		$widget .='<div class="'.$cl.'"><label';

		$widget .='>';

		$widget .=  $this->buildInputRadio($value, $isChecked, $attr);
  	
		$widget .= $name.'</label></div>';
	  }
	  return $widget;
  }

  protected function buildButtonsInline()
  {
	  $widget = '';
	  $attr = array();

	  if($this->required)
		{
			$class = 'btn btn-default';
			$attr[] = 'class="sr-only" required';
		}
		else{
			$class = 'radio-inline';
		}

	  foreach($this->buttons as $name => $value)
	  {
		$isChecked = ($value == $this->value) && isset($this->value);

		if($isChecked && $this->required)
			$cl = $class.' active';
		else
			$cl = $class;
		
		$widget .='<label class="'.$cl.'">';

		$widget .=  $this->buildInputRadio($value, $isChecked, $attr);
  	
		$widget .= $name.'</label>';
	  }

	  return $widget;
  }

  protected function buildInputRadio($value, $isChecked = false, array $attr = array())
  {
	  $radio = '<input type="radio" name="'.$this->getFullName().'"';
    
	  $radio .= ' value="'.htmlspecialchars($value).'" ';

    
    if ($isChecked)
    {
      $radio .= 'checked ';
    }

	$radio .= implode(" ", $attr);

	if (!empty($this->attributes))
    {
		foreach($this->attributes as $attr => $val)
			{
				$radio .= ' '.$attr.'="'.$val.'"';
			}
    }
	
	return $radio .= ' />'; //end of radio type
  }
}