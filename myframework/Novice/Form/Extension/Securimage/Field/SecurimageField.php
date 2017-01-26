<?php
namespace Novice\Form\Extension\Securimage\Field;

use Novice\Form\Extension\Securimage\Securimage;

class SecurimageField extends \Novice\Form\Field\Field
{
  protected $options = array(
		'show_image_url' => null,
        'image_id' => 'captcha_image',
        'image_alt_text' => 'CAPTCHA Image',
        'show_audio_button' => true,
        'disable_flash_fallback' => false,
        'show_refresh_button' => true,
        'refresh_icon_url' => null,
        'audio_button_bgcol' => '#ffffff',
        'audio_icon_url' => null,
        'loading_icon_url' => null,
        'icon_size' => 24,
        'audio_play_url' => null,
        'audio_swf_url' => null,
        'show_text_input' => true,
        'refresh_alt_text' => 'Refresh Image',
        'refresh_title_text' => 'Refresh Image',
        'input_text' => 'Type the text:',
        'input_attributes' => array(),
        'image_attributes' => array(),
        'error_html' => null,
        'namespace' => '',
		'securimage_path' => null,
  );

  public function __construct(array $options = array())
  {
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

	$widget .= $this->buildLabelTag(); // label tag

	$widget .= $this->buildMessage();

	//$widget .= '<div class="bg-primary" style="border-radius: 5px; padding: 3px;">';

	/*$docroot = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME']));
    $docroot = realpath($docroot);
	$si = new \Securimage();
	$r = new \ReflectionObject($si);

	$this->options['securimage_path'] = str_replace($docroot, '', dirname($r->getFileName()));*/

	$this->options['input_id'] = $this->getId();

	$this->options['input_name'] = $this->getFullName();

	$widget .= Securimage::getCaptchaHtml($this->options);

	//$widget .= '</div>';

    
    return $widget .= '</div>';
  }

}