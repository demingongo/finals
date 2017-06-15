<?php
namespace Rgs\UserModule\Form;

use Novice\Form\FormBuilder;
use Novice\Form\Field\InputField,
	Novice\Form\Field\RadioField,
	Novice\Form\Field\CheckboxField,
	Novice\Form\Field\SelectField,
	Novice\Form\Extension\Securimage\Field\SecurimageField;
use Novice\Form\Validator\MaxLengthValidator,
	Novice\Form\Validator\MinLengthValidator,
	Novice\Form\Validator\NotNullValidator,
	Novice\Form\Validator\EmailValidator,
	Novice\Form\Validator\NonRequiredEmailValidator,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator;
use Rgs\UserModule\Entity\Group;

class RegisterFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'user_registration_regiser_form';
	}

	private function getGroups()
	{
		$retour = array();

		$em = $this->container->get('managers')->getManager();

		$repository = $em->getRepository('UserModule:Group');

		$groups = $repository->findBy(
				array(),
				array('name' => 'ASC', 'locked' => Group::NOT_LOCKED),
				null,
				null
			);

		foreach($groups as $group){
			$retour[$group->getId()] = $group->getName();
		}
		
		return $retour;
	}
  
  public function build()
  {

	$translator = $this->container->get('translator');
	$domain = "UserModule";
	
	$username = $translator->trans('security.login.username', array(), $domain);
	$password = $translator->trans('security.login.password', array(), $domain);

    $this->form->add(new InputField(array(
        'label' => $username,
		'type' => 'text',
		'title' => $translator->trans('Minimum %nb% caractères : 
chiffres, lettres, tirets, underscores et apostrophes', array('%nb%' => 6)),
        'name' => 'login',
        //'maxlength' => 50,
		'placeholder' => $translator->trans('form.placeholder.username', array(), $domain),
		'pattern' => "^([_A-z0-9'-]){6,}$",
	    'required' => true,
		'control_label' => true,
		'validators' => array(
    new MaxLengthValidator( $translator->trans('Maximum %nb% characters', array('%nb%' => 30)), 30),
    new NotNullValidator( $translator->trans('Login is required') ),)
		)))
		->add(new InputField(array(
        'label' => 'Email',
		'type' => 'email',
		'title' => $translator->trans('form.placeholder.email', array(), $domain),
        'name' => 'email',
        'maxlength' => 50,
		'placeholder' => $translator->trans('form.placeholder.email', array(), $domain),
	    'required' => true,
		'addon' => '@',
		'attributes' => array('data-error' => $translator->trans('That email address is invalid') ),
		'validators' => array(
	new EmailValidator('Email invalid', true, true),)
		)))
		->add(new InputField(array(
        'label' => $password,
		'type' => 'password',
		'title' => $translator->trans('Minimum %nb% characters', array('%nb%' => 6)),
        'name' => 'password',
        'maxlength' => 24,
		'placeholder' => $password,
	    'required' => true,
		'control_label' => true,
		'feedback_icon' => true,
		//'always_empty' => false,
		'attributes' => array('data-minlength' => '6',),
		'validators' => array(
    new MinLengthValidator( $translator->trans('Minimum %nb% characters', array('%nb%' => 6)), 6),)
		)))
		->add(new InputField(array(
        'label' => '&nbsp;',
		'type' => 'password',
		'title' => $translator->trans('form.placeholder.password_confirmation', array(), $domain),
        'name' => 'confirm',
        'maxlength' => 24,
		'placeholder' => $translator->trans('form.placeholder.password_confirmation', array(), $domain),
	    'required' => true,
		'control_label' => true,
		'feedback_icon' => true,
		'attributes' => array('data-match' => '#'.$this->form->getName().'_password',
							  'data-match-error' => $translator->trans('Whoops, passwords don\'t match') ),
		)));

		/*$options = array(
			'name' => 'Image',
			'type' => 1,
			'label' => 'Image',
			//'input_attributes' => array("required" => "required"),
			'text_remove_btn' => '&times;',
			'akeys' => array(md5('one'), md5('gfk'), md5('theodore')),
		);

		$this->form->addExtension(new \Novice\Form\Extension\Filemanager\FilemanagerExtension('/plugins/filemanager/filemanager', $options));*/
  }
}