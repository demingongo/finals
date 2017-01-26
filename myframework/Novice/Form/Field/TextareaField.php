<?php
namespace Novice\Form\Field;

class TextareaField extends Field
{
  protected $cols;
  protected $rows;
  protected $placeholder;
  protected $class;
  
  public function buildWidget()
  {
    $widget = '<span class="';

	if (!empty($this->errorMessage))
	{
			$widget .= ' has-error';
	}
	else if ($this->warningMessage !== null)
	{
			$widget .= ' has-warning';
	}
	else if (empty($this->errorMessage) && !empty($this->value))
	{
			$widget .= ' has-success';
	}

	$widget .= '">';
    
    $widget .= $this->buildLabelTag(); // label tag

	$widget .= $this->buildMessage();
	
	$widget .= '<textarea id="'.$this->getId().'" name="'.$this->getFullName().'"';
	
	$widget .= ' class="form-control';
	
	if (!empty($this->class))
    {
      $widget .= ' '.$this->class;
    }
	
	$widget .= '"';
    
    if (!empty($this->cols))
    {
      $widget .= ' cols="'.$this->cols.'"';
    }
    
    if (!empty($this->rows))
    {
      $widget .= ' rows="'.$this->rows.'"';
    }

	if (!empty($this->placeholder))
    {
      $widget .= ' placeholder="'.htmlspecialchars($this->placeholder).'"';
    }

	if (!empty($this->title))
    {
      $widget .= ' title="'.htmlspecialchars($this->title).'"';
    }

	if (!empty($this->attributes))
    {
		foreach($this->attributes as $attr => $val)
			{
				$widget .= ' '.$attr.'="'.$val.'"';
			}
    }
    
    $widget .= '>';
    
    if (!empty($this->value))
    {
      $widget .= htmlspecialchars($this->value);
    }
    
    return $widget.'</textarea></span>';
  }
  
  public function setCols($cols)
  {
    $cols = (int) $cols;
    
    if ($cols > 0)
    {
      $this->cols = $cols;
    }
  }
  
  public function setRows($rows)
  {
    $rows = (int) $rows;
    
    if ($rows > 0)
    {
      $this->rows = $rows;
    }
  }

  public function setPlaceholder($placeholder)
  {
    $this->placeholder = (string) $placeholder;
  }

  public function setClass($class)
  {
    $this->class = (string) $class;
  }
}