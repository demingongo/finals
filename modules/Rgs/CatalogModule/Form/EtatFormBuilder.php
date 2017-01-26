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

use Rgs\CatalogModule\Entity\Etat;

class EtatFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'etat_form';
	}
  
  public function build()
  {
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
		->add(new RadioField(array(
        'label' => $trans('form.status'),
		'control_label' => true,
        'name' => 'published',
		'inline' => 1,
		//'required' => true,
		'buttons' => array('Published' => Etat::PUBLISHED , 'Unpublished' => Etat::NOT_PUBLISHED),
		'validators' => array(
    new NotNullValidator('Choisir le statut'),)
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
  }
}