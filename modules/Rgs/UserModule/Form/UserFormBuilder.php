<?php
namespace Rgs\UserModule\Form;

use Novice\Form\FormBuilder;
use Novice\Form\Field\InputField,
	Novice\Form\Field\RadioField,
	Novice\Form\Field\CheckboxField,
	Novice\Form\Field\SelectField,
	Novice\Form\Field\Prototype,
	Novice\Form\Extension\Securimage\Field\SecurimageField;
use Novice\Form\Validator\MaxLengthValidator,
	Novice\Form\Validator\MinLengthValidator,
	Novice\Form\Validator\NotNullValidator,
	Novice\Form\Validator\EmailValidator,
	Novice\Form\Validator\NonRequiredEmailValidator,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator,
	Novice\Form\Extension\Entity\EntityExtension;
use Rgs\UserModule\Entity\Group,
	Rgs\UserModule\Entity\User;

class UserFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'user_form';
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
	
	$trans = function($string, array $array = array(), $lang = null) use ($translator, $domain){
		return $translator->trans($string, $array, $domain, $lang);
	};
	

    $this->form->add(new InputField(array(
        'label' => $trans('user.login'),
		'type' => 'text',
		'title' => 'Minimum 6 caractères : 
chiffres, lettres, tirets, underscores et apostrophes',
        'name' => 'login',
        //'maxlength' => 50,
		'placeholder' => $translator->trans('form.placeholder.username', array(), $domain),
		'pattern' => "^([_A-z0-9'-]){6,}$",
	    'required' => true,
		'control_label' => true,
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (30 caractères maximum)', 30),
    new NotNullValidator('Spécifiez le login'),)
		)))
		->add(new InputField(array(
        'label' => 'Email',
		'type' => 'email',
		'title' => 'Write your email address',
        'name' => 'email',
        'maxlength' => 50,
		'placeholder' => $translator->trans('form.placeholder.email', array(), $domain),
	    'required' => true,
		'addon' => '@',
		'attributes' => array('data-error' => 'That email address is invalid'),
		'validators' => array(
	new EmailValidator('Email invalid', true, true),)
		)));
		
		// SHOULD NOT BE SHOWN FOR MYSELF (USER SESSION)
		$this->form->add(new RadioField(array(
        'label' => $trans('user.activated'),
		'control_label' => true,
        'name' => 'activated',
		 'attributes' => array('class' => 'icheck iradio_line-red'),
		'inline' => 1,
		'required' => true,
		'buttons' => array('Yes' => User::ACTIVATED , 'No' => User::NOT_ACTIVATED),
		'validators' => array(new NotNullValidator('Faire un choix !'))
		)))
		->add(new RadioField(array(
        'label' => $trans('user.not_locked'),
		'control_label' => true,
        'name' => 'locked',
		 'attributes' => array('class' => 'icheck iradio_line-red'),
		'inline' => 1,
		'required' => true,
		'buttons' => array('Yes' => User::NOT_LOCKED , 'No' => User::LOCKED),
		'validators' => array(new NotNullValidator('Faire un choix !'))
		)));
		
		$this->form->add(new Prototype(array('name'=>'group')))
		->add(new InputField(array(
        'label' => $trans('user.address'),
		'type' => 'text',
		'title' => 'Adresse : 
chiffres, lettres, tirets, underscores, espaces et apostrophes',
        'name' => 'address',
		'placeholder' => $trans('user.address'),
		'pattern' => "^([ _A-z0-9'-]){0,64}$",
		'control_label' => true,
		)))
		->add(new InputField(array(
        'label' => $trans('user.code'),
		'type' => 'text',
		'title' => 'Maximum 6 caractères : 
chiffres, lettres, tirets',
        'name' => 'codePostal',
		'placeholder' => $trans('user.code'),
		'pattern' => "^([A-z0-9-]){0,6}$",
		'control_label' => true,
		'validators' => array(
    new MaxLengthValidator('Le code postal spécifié est trop long (6 caractères maximum)', 6),)
		)))
		->add(new InputField(array(
        'label' => $trans('user.city'),
		'type' => 'text',
        'name' => 'ville',
		'placeholder' => $trans('user.city'),
		'pattern' => "^([ _A-z0-9'-]){0,24}$",
		'control_label' => true,
		)))
		->add(new InputField(array(
        'label' => $trans('user.tel'),
		'type' => 'text',
        'name' => 'tel',
		'placeholder' => $trans('user.tel'),
		'pattern' => "^([+])*([0-9])*$",
		'control_label' => true,
		)))
		->add(new InputField(array(
        'label' => $trans('user.mobile'),
		'type' => 'text',
        'name' => 'mobile',
		'placeholder' => $trans('user.mobile'),
		'pattern' => "^([+])*([0-9])*$",
		'control_label' => true,
		)))
		->add(new InputField(array(
        'label' => $trans('user.last_login'),
		'type' => 'text',
        'name' => 'last_login_to_string',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('user.created_at'),
		'type' => 'text',
        'name' => 'created_at_to_string',
		'validation_states' => false,
		//'bootstrap' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('user.updated_at'),
		'type' => 'text',
        'name' => 'updated_at_to_string',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('user.slug'),
		'type' => 'text',
        'name' => 'slug',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('form.placeholder.new_password'),
		'type' => 'password',
		'title' => 'Minimum 6 caractères',
        'name' => 'password_update',
        'maxlength' => 24,
		'placeholder' => $trans('form.placeholder.new_password'),
	    //'required' => true,
		'control_label' => true,
		//'feedback_icon' => true,
		//'always_empty' => false,
		'attributes' => array('data-minlength' => '6',),
		/*'validators' => array(
    new MinLengthValidator('Minimum 6 caractères', 6),)*/
		)))
		->add(new InputField(array(
        'label' => '&nbsp;',
		'type' => 'password',
		'title' => $trans('form.placeholder.password_confirmation'),
        'name' => 'confirm',
        'maxlength' => 24,
		'placeholder' => $trans('form.placeholder.password_confirmation'),
	    //'required' => true,
		'control_label' => true,
		//'feedback_icon' => true,
		'attributes' => array('data-match' => '#'.$this->form->getName().'_password_update',
							  'data-match-error' => 'Whoops, these don\'t match'),
		)));
		
		$this->form->addExtension(new EntityExtension($this->container->get('managers'), array(
		'class' => 'UserModule:Group',
		'choice_label' => 'name',
        'label' => $trans('user.group'),
		'control_label' => true,
        'name' => 'group',
		'attributes' => 
			array(
				'style' => 'width: 99%',
				'data-placeholder' => 'Choisir une marque',
				//'data-theme' => 'classic',
				'data-allow-clear' => 'true',
				'data-minimum-results-for-search' => 15,
				'class' => 'select2',
				//'onchange' => 'this.form.submit()',
			),
		'validators' => array(new NotNullValidator('Choisir le groupe')),
		//'options' => $this->getGroups(),
		)));
  }
}