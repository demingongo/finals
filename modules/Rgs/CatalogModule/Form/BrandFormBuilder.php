<?php
namespace Rgs\CatalogModule\Form;

use Novice\Form\FormBuilder;
use Novice\Form\Field\InputField,
	Novice\Form\Field\RadioField,
	Novice\Form\Field\CheckboxField,
	Novice\Form\Field\SelectField,
	Novice\Form\Field\TextareaField,
	Novice\Form\Field\Prototype,
	Novice\Form\Extension\Securimage\Field\SecurimageField;
use Novice\Form\Validator\MaxLengthValidator,
	Novice\Form\Validator\MinLengthValidator,
	Novice\Form\Validator\NotNullValidator,
	Novice\Form\Validator\EmailValidator,
	Novice\Form\Validator\NonRequiredEmailValidator,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator;

use Rgs\CatalogModule\Entity\Brand;

class BrandFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'brand_form';
	}
  
  public function build()
  {
	$dropdown = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="#">Action</a></li>
          <li><a href="#">Another action</a></li>
          <li><a href="#">Something else here</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="#">Separated link</a></li>
        </ul><!-- /btn-group -->';

	$dropdown2 = '<button type="button" class="btn btn-default">Action </button>
	<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	<span class="caret"></span>
	<span class="sr-only">Toggle Dropdown</span></button>
        <ul class="dropdown-menu">
          <li><a href="#">Action</a></li>
          <li><a href="#">Another action</a></li>
          <li><a href="#">Something else here</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="#">Separated link</a></li>
        </ul><!-- /btn-group -->';

	$translator = $this->container->get('translator');
	$domain = "RgsCatalogModule";

	$trans = function($string, array $array = array(), $lang = null) use ($translator, $domain){
		return $translator->trans($string, $array, $domain, $lang);
	};

    $this->form->add(new InputField(array(
        'label' => $trans('form.title'),
		'type' => 'text',
        'name' => 'name',
        'maxlength' => 64,
		'pattern' => "^[^ ].{1,}$",
	    'required' => true,
		'control_label' => true,
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (64 caractères maximum)', 64),
    new NotNullValidator('Spécifiez le titre'),)
		)))
		//Prototype is used to order the widget 'logo' after 'name' and before 'published' 
		->add(new Prototype(array('name' => 'logo')))  
		->add(new RadioField(array(
        'label' => $trans('form.status'),
		'control_label' => true,
        'name' => 'published',
		'required' => true,
		'inline' => 1,
		//'required' => true,
		'buttons' => array('Published' => Brand::PUBLISHED , 'Unpublished' => Brand::NOT_PUBLISHED),
		'validators' => array(
    new NotNullValidator('Choisir le statut'),)
		)))
		->add(new InputField(array(
        'label' => "Lien externe (URL)",
		'type' => 'url',
        'name' => 'url',
		'title' => 'lien vers site le officiel ou autres informations concernant la marque',
		'control_label' => true
		)))
		->add(new InputField(array(
        'label' => $trans('form.created_at'),
		'type' => 'text',
        'name' => 'created_at_to_string',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('form.updated_at'),
		'type' => 'text',
        'name' => 'updated_at_to_string',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('form.slug'),
		'type' => 'text',
        'name' => 'slug',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)));

		$options = array(
			'name' => 'logo',
			'type' => 1,
			'label' => $trans('form.image'),
			'text_remove_btn' => '&times;',
			'akeys' => array(md5('one'), md5('gfk'), md5('theodore')),
		);

		$this->form->addExtension(new \Novice\Form\Extension\Filemanager\FilemanagerExtension('/plugins/filemanager/filemanager', $options));
  }
}