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

class LostPasswordFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'user_security_lostpassword_form';
	}
  
  public function build()
  {
	  
	$translator = $this->container->get('translator');
	$domain = "UserModule";

	$username = $translator->trans('security.login.username', array(), $domain);

    $this->form->add(new InputField(array(
        'label' => $username,
		'type' => 'text',
		'title' => 'Minimum 6 caractères : 
chiffres, lettres, tirets, underscores et apostrophes',
        'name' => 'login',
        'maxlength' => 30,
		'placeholder' => $translator->trans('form.placeholder.username', array(), $domain),
		'pattern' => "^([_A-z0-9'-]){6,}$",
	    'required' => true,
		'control_label' => true,
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (30 caractères maximum)', 30),
    new NotNullValidator('Spécifiez le login'),)
		)));
  }
}