<?php
namespace Novice\Form\Field;

class InputField extends Field
{
  //informations sur la balise input : http://www.w3schools.com/tags/tag_input.asp

  protected $alt; //alt attribute can only be used with <input type="image">

  protected $autocomplete; //The autocomplete attribute works with the following <input> types: text, search, url, tel, email, password, datepickers, range, and color

  protected $autofocus;

  protected $disabled; //disabled attribute will not work with <input type="hidden">

  protected $form;
  
  protected $formenctype; //formenctype attribute is used with type="submit" and type="image"

  protected $formtarget; //formtarget attribute can be used with type="submit" and type="image"

  protected $height; //height attribute is used only with <input type="image">
  protected $width; //width attribute is used only with <input type="image">

  protected $datalist_id; //Refers to a <datalist> element that contains pre-defined options for an <input> element

  protected $max;
  protected $min; //max and min attributes works with the following input types: number, range, date, datetime, datetime-local, month, time and week
  
  protected $maxlength;

  protected $multiple; //multiple attribute works with the following input types: email, and file

  protected $pattern; //pattern attribute works with the following input types: text, search, url, tel, email, and password

  protected $placeholder; //placeholder attribute works with the following input types: text, search, url, tel, email, and password

  protected $readonly;

  protected $size; //size attribute works with the following input types: text, search, tel, url, email, and password
  
  protected $src; //src attribute is required for <input type="image">, and can only be used with <input type="image">

  protected $step; //step attribute works with the following input types: number, range, date, datetime, datetime-local, month, time and week

  protected $type = 'text';

  protected $always_empty = true;

  protected $class;

  protected $left_addon;
  protected $right_addon;
  protected $left_addon_class = "input-group-addon";
  protected $right_addon_class = "input-group-addon";

  protected $feedback_icon = false;

  //protected $formaction; //formaction attribute is used with type="submit" and type="image"
  //protected $formmethod; //formmethod attribute can be used with type="submit" and type="image"
  //protected $formnovalidate;

  
  
  
  public function buildWidget()
  {
    $widget = '<span class=" '; //col-xs-6

	if($this->feedback){
		if (!empty($this->errorMessage))
		{
			$widget .= ' has-error';
		}
		else if ($this->warningMessage !== null)
		{
			$widget .= ' has-warning';
		}
		else if (empty($this->errorMessage) && !empty($this->value) && $this->type != "password")
		{
			$widget .= ' has-success';
		}
	}

	$widget .= '">';   

	$widget .= $this->buildLabelTag(); // label tag

	$widget .= $this->buildMessage();

	/*if (!empty($this->errorMessage))
	{
			$widget .= ' <small';
			if ($this->control_label)
    		{
      			$widget .= ' class="control-label"';
    		}
			$widget .= '>'.htmlspecialchars($this->errorMessage).'</small>';
	}
	else if(!empty($this->warningMessage))
	{
		$widget .= ' <small';
			if ($this->control_label)
    		{
      			$widget .= ' class="control-label"';
    		}
		$widget .= '>'.htmlspecialchars($this->warningMessage).'</small>';
	}*/


	if ($hasAddon = !empty($this->left_addon) || !empty($this->right_addon))
	{
		$widget .= '<span class="input-group">';
	}

	if (!empty($this->left_addon))
	{
		$widget .= '<span class="'.htmlspecialchars($this->left_addon_class).'">'.$this->left_addon.'</span>';
	}

	$widget .= $this->buildInputTag(); // input tag	

	if (!empty($this->right_addon))
	{
		$widget .= '<span class="'.htmlspecialchars($this->right_addon_class).'">'.$this->right_addon.'</span>';
	}

	if ($hasAddon)
	{
		$widget .= '</span>';
	}

	if ($this->feedback && $this->feedback_icon)
	{
		if (!empty($this->errorMessage))
		{
			$widget .= '
			<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
			<span id="'.$this->getId().'_status" class="sr-only">(error)</span>';
		}
		else if (!empty($this->warningMessage))
		{
			$widget .= '
			<span class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>
			<span id="'.$this->getId().'_status" class="sr-only">(warning)</span>';
		}
		else if (empty($this->errorMessage) && !empty($this->value) && $this->type != "password")
		{
			$widget .= '
			<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span id="'.$this->getId().'_status" class="sr-only">(success)</span>';
		}
		else{
			$widget .= '
			<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
			<span id="'.$this->getId().'_status" class="sr-only">(status)</span>';
		}
	}
    
    return $widget .= '</span>';
  }

  public function setAlt($alt)
  {
	if($this->type == 'image'){
      $this->alt = (string) $alt;
	}
	else{
		throw new \RuntimeException('"alt" attribute can only be used with <input type="image">');
	}
  }

  public function setAutocomplete($autocomplete)
  {
	$autocomplete = strtolower($autocomplete);
	switch($this->type){
		case "text":
		case "search":
		case "url":
		case "tel":
		case "email":
		case "password":
		case "date":
		case "datetime":
		case "datetime-local":
		case "range":
		case "color":
			if($autocomplete == "on" || $autocomplete == "off"){
				$this->autocomplete = $autocomplete;
			}
			else{
				throw new \RuntimeException('"autocomplete" attribute value must be "on" or "off"');
			}
		break;
		default:
			throw new \RuntimeException('"autocomplete" attribute works with the following <input> types: text, search, url, tel, email, password, datepickers, range, and color');
		break;
	}
  }

  public function setAutofocus($autofocus)
  {
	$autofocus = strtolower($autofocus);
	if($autofocus == "autofocus" || empty($autofocus)){
      $this->autofocus = "autofocus";
	}
  }

  public function setDisabled($disabled)
  {
	$disabled = strtolower($disabled);

		if($disabled == "disabled" || empty($disabled)){
			$this->disabled = "disabled";
		}
  }

  public function setForm($form)
  {
      $this->form = (string) $form;
  }

  public function setFormenctype($formenctype)
  {
	  switch($this->type){
		case "image":
		case "submit":
			$this->formenctype = (string) $formenctype;
		break;
		default:
			throw new \RuntimeException('"formenctype" attribute is used with type="submit" and type="image"');
		break;
	  }
  }

  public function setFormtarget($formtarget)
  {
	  switch($this->type){
		case "image":
		case "submit":
			$this->formtarget = (string) $formtarget;
		break;
		default:
			throw new \RuntimeException('"formtarget" attribute is used with type="submit" and type="image"');
		break;
	  }
  }

  public function setHeight($height)
  {
	  if($this->type == 'image' && is_numeric($height)){
		$this->height = $height;
	  }
	  else{
		  throw new \RuntimeException('"height" attribute is used only with <input type="image"> and its value must be numeric');
	  }
  }

  public function setWidth($width)
  {
	  if($this->type == 'image' && is_numeric($width)){
		$this->width = $width;
	  }
	  else{
		  throw new \RuntimeException('"width" attribute is used only with <input type="image"> and its value must be numeric');
	  }
  }

  public function setDatalist_id($list)
  {
      $this->datalist_id = (string)$list;
  }

  public function setMax($max)
  {
	  switch($this->type){
		case "number":
		case "range":
		case "date":
		case "datetime":
		case "datetime-local":
		case "month":
		case "time":
		case "week":
			if(is_numeric($max)){
				$this->max = $max;
			}
			else{
				throw new \RuntimeException('"max" attribute value must be numeric');
			}
		break;
		default:
			throw new \RuntimeException('"max" attribute works with the following input types: number, range, date, datetime, datetime-local, month, time and week');
		break;
	  }
  }

  public function setMin($min)
  {
	  switch($this->type){
		case "number":
		case "range":
		case "date":
		case "datetime":
		case "datetime-local":
		case "month":
		case "time":
		case "week":
			if(is_numeric($min)){
				$this->min = $min;
			}
			else{
				throw new \RuntimeException('"min" attribute value must be numeric');
			}
		break;
		default:
			throw new \RuntimeException('"min" attribute works with the following input types: number, range, date, datetime, datetime-local, month, time and week');
		break;
	  }
  }

  public function setMaxlength($maxlength)
  {
    $maxlength = (int) $maxlength;
    
    if ($maxlength > 0)
    {
      $this->maxlength = $maxlength;
    }
    else
    {
      throw new \RuntimeException('attribut "maxlength" doit être un nombre supérieur à 0');
    }
  }

  public function setMultiple($multiple)
  {
	switch($this->type){
		case "email":
		case "file":
			$this->multiple = (bool) $multiple;
		break;
		default:
		break;
	}
  }

  public function setPattern($pattern)
  {
    $this->pattern = (string) $pattern;
  }

  public function setPlaceholder($placeholder)
  {
    $this->placeholder = (string) $placeholder;
  }

  public function setReadonly($readonly)
  {
	$this->readonly = (bool) $readonly;
  }

  public function setRequired($required)
  {
		switch($this->type){
			case "text":
			case "search":
			case "url":
			case "tel":
			case "email":
			case "password":
			case "date":
			case "datetime":
			case "datetime-local":
			case "number":
			case "checkbox":
			case "radio":
			case "file":
				$this->required = (bool) $required;
			break;
			default:
			break;
		}	
  }

  public function setSize($size)
  {
	  switch($this->type){
		case "text":
		case "search":
		case "url":
		case "tel":
		case "email":
		case "password":
			$size = (int) $size;
			if ($size > 0)
			{
				$this->size = $size;
			}
			else
			{
				throw new \RuntimeException('attribut "size" doit être un nombre supérieur à 0');
			}
		break;
		default:
			throw new \RuntimeException('"size" attribute works with the following input types: text, search, tel, url, email, and password');
		break;
	}
  }

  public function setSrc($src)
  {
	  if($this->type == 'image'){
		$this->src = $src;
	  }
	  else{
		  throw new \RuntimeException('"src" attribute is required for <input type="image">, and can only be used with <input type="image">');
	  }
  }

  public function setStep($step)
  {
	  switch($this->type){
		case "number":
		case "range":
		case "date":
		case "datetime":
		case "datetime-local":
		case "month":
		case "time":
		case "week":
			if (is_numeric($step) && $step > 0 )
			{
				$this->step = $step;
			}
			else
			{
				throw new \RuntimeException('attribut "step" doit etre un nombre superieur à 0');
			}
		break;
		default:
			throw new \RuntimeException('"step" attribute works with the following input types: number, range, date, datetime, datetime-local, month, time and week');
		break;
	}
  }

  public function setType($type)
  {
	$type = strtolower($type);
	switch($type){
		case "checkbox":
		case "file":
		case "radio":
			throw new \RuntimeException('Pour creer un input type="'.$type.'", utilisez plutot une instance de "'.ucfirst($type).'Field"');
		break;
	}

	$types = array(	"button",
					"color",
					"date",
					"datetime",
					"datetime-local",
					"email",
					"file",
					"hidden",
					"image",
					"month",
					"number",
					"password",
					"range",
					"reset",
					"search",
					"submit",
					"tel",
					"text",
					"time",
					"url",
					"week");

	if(in_array($type , $types)){
		$this->type = $type;
	}
	else{
		throw new \RuntimeException('Unknown value for type="'.$type.'"');
	}
  }

  public function setClass($class)
  {
    $this->class = (string) $class;
  }

  public function setAddon($addon)
  {
    $this->left_addon = (string) $addon;
  }

  public function setLeft_addon($addon)
  {
    $this->left_addon = (string) $addon;
  }

  public function setRight_addon($addon)
  {
    $this->right_addon = (string) $addon;
  }

  public function setLeft_addon_class($addon_class)
  {
    $this->left_addon_class = (string) $addon_class;
  }

  public function setRight_addon_class($addon_class)
  {
    $this->right_addon_class = (string) $addon_class;
  }

  public function setAlways_empty($bool)
  {
	  $this->always_empty = (bool) $bool;
  }

  public function setFeedback_icon($feedback_icon)
  {
    $this->feedback_icon = (bool) $feedback_icon;
  }

  /*protected function buildLabelTag()
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
  }*/

  protected function buildInputTag()
  {
	$widget = '<input type="'.$this->type.'" id="'.$this->getId().'" name="'.$this->getFullName().'"';

	$widget .= ' class="';

	if($this->bootstrap){
		$widget .= 'form-control';
	}
	
	if (!empty($this->class))
    {
      $widget .= ' '.$this->class;
    }
	
	$widget .= '"';

	if (!empty($this->attributes))
    {
		foreach($this->attributes as $attr => $val)
			{
				$widget .= ' '.$attr.'="'.$val.'"';
			}
    }

	if ($this->type == 'image')
    {
      $widget .= ' src="'.htmlspecialchars($this->src).'"';
    }

	if (!empty($this->pattern))
    {
      $widget .= ' pattern="'.htmlspecialchars($this->pattern).'"';
    }

	if (!empty($this->placeholder))
    {
      $widget .= ' placeholder="'.htmlspecialchars($this->placeholder).'"';
    }

	if (!empty($this->alt))
    {
      $widget .= ' alt="'.htmlspecialchars($this->alt).'"';
    }

	if (!empty($this->autocomplete))
    {
      $widget .= ' autocomplete="'.$this->autocomplete.'"';
    }

	if (!empty($this->autofocus))
    {
      $widget .= ' autofocus="'.$this->autofocus.'"';
    }

	if (!empty($this->disabled))
    {
      $widget .= ' disabled="'.$this->disabled.'"';
    }

	if (!empty($this->form))
    {
      $widget .= ' form="'.$this->form.'"';
    }

	if (!empty($this->formenctype))
    {
      $widget .= ' formenctype="'.$this->formenctype.'"';
    }

	if (!empty($this->formtarget))
    {
      $widget .= ' formtarget="'.$this->formtarget.'"';
    }

	if (!empty($this->height))
    {
      $widget .= ' height="'.$this->height.'"';
    }

	if (!empty($this->width))
    {
      $widget .= ' width="'.$this->width.'"';
    }

	if (!empty($this->datalist_id))
    {
      $widget .= ' list="'.$this->datalist_id.'"';
    }

	if (!empty($this->max))
    {
      $widget .= ' max="'.$this->max.'"';
    }

	if (is_numeric($this->min))
    {
      $widget .= ' min="'.$this->min.'"';
    }

	if (!empty($this->multiple))
    {
      $widget .= ' multiple="'.$this->multiple.'"';
    }

	if ($this->readonly)
    {
      $widget .= ' readonly="readonly"';
    }

	if ($this->required)
    {
      $widget .= ' required="required"';
    }

	if (!empty($this->size))
    {
      $widget .= ' size="'.$this->size.'"';
    }

	if (is_numeric($this->step))
    {
      $widget .= ' step="'.$this->step.'"';
    }
    
    if (!empty($this->maxlength))
    {
      $widget .= ' maxlength="'.$this->maxlength.'"';
    }

	if (!empty($this->title))
    {
      $widget .= ' title="'.htmlspecialchars($this->title).'"';
    }

	if (!empty($this->value) && ($this->type != "password" || ($this->type == "password" && !$this->always_empty)))
    {
      $widget .= ' value="'.htmlspecialchars($this->value).'"';
    }

	if ($this->feedback_icon)
	{
		$widget .= ' aria-describedby="'.$this->getId().'_status"';
	}

	return $widget .= ' />'; //end of input tag

  }


}