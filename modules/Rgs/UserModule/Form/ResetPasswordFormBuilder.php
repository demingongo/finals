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

class ResetPasswordFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'user_security_resetpassword_form';
	}
  
  public function build()
  {

	$translator = $this->container->get('translator');
	$domain = "UserModule";
	
	$password = $translator->trans('security.login.password', array(), $domain);

    $this->form
		->add(new InputField(array(
        'label' => $password,
		'type' => 'password',
		'title' => 'Minimum 6 caractères',
        'name' => 'password',
        'maxlength' => 24,
		'placeholder' => $password,
	    'required' => true,
		'control_label' => true,
		'feedback_icon' => true,
		//'always_empty' => false,
		'attributes' => array('data-minlength' => '6',),
		'validators' => array(
    new MinLengthValidator('Minimum 6 caractères', 6),)
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
							  'data-match-error' => 'Whoops, these don\'t match'),
		)));

  }
}