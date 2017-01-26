<?php
namespace Novice\Form\Extension\Filemanager\Field;

use Novice\Form\Field\Field;
use Novice\Form\Extension\Filemanager\Filemanager;

class FilemanagerField extends Field
{
  protected $options = array(
		'type' => null,
        'fldr' => null,
        'sort_by' => null,
        'descending' => null,
        'lang' => null,
        'relative_url' => null,
        'popup' => null,
        'text_open_btn' => null,
        'show_remove_btn' => null,
        'text_remove_btn' => null,
        'show_input' => null,
        'input_attributes' => array(),
        'base_url' => null,
	    'filemanager_path' => null,
		'akey' => null,
  );

  public function __construct(array $options = array())
  {
	//dump($options);
    parent::__construct($options);
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
	  else if(in_array($type, array_keys($this->options))){
		  $this->options[$type] = $value;
	  }
    }
  }  
  
  public function buildWidget()
  {
    $widget = '<div class=" '; //col-xs-6

	if (!empty($this->errorMessage))
	{
			$widget .= ' has-error';
	}
	else if (!empty($this->warningMessage))
	{
			$widget .= ' has-warning';
	}

	$widget .= '">'; 
	
	$widget .= '<div class="input-group">';

	$widget .= $this->buildLabelTag(); // label tag

	$widget .= $this->buildMessage();

	$this->options['input_id'] = $this->getId();

	$this->options['input_name'] = $this->getFullName();

	$this->options['value'] = htmlspecialchars($this->value());

	/*dump($this->options);
		exit(__METHOD__);*/

	$widget .= Filemanager::getHtml($this->options);

	$widget .= '</div>';

    
    return $widget .= '</div>';
  }

}