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

class LoginFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'user_security_login_form';
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
	$domain = "UserModule";

	$username = $translator->trans('security.login.username', array(), $domain);
	$password = $translator->trans('security.login.password', array(), $domain);

    $this->form->add(new InputField(array(
        'label' => $username,
		'type' => 'text',
		'title' => 'Minimum 6 caractères : 
chiffres, lettres, tirets, underscores et apostrophes',
        'name' => 'login',
        //'maxlength' => 50,
		'placeholder' => $translator->trans('form.placeholder.username', array(), $domain),
		'pattern' => "^([_A-z0-9'-]){6,}$",
	    'required' => true,
		'control_label' => true,
		//'addon' => '@', //'&euro;',
		/*'right_addon_class' => 'input-group-btn',
		'right_addon' => $dropdown2,//'<input type="checkbox" aria-label="...">',*/ //'<button class="btn btn-default" type="button">Go!</button>',
		//'attributes' => array('data-error' => 'something to write'),
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (30 caractères maximum)', 30),
    new NotNullValidator('Spécifiez le login'),)
		)))
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
		)));

		$this->form->add(new CheckboxField(array(
        'label' => '',
		'control_label' => true,
        'name' => 'remember_me',
		//'required' => true,
		'buttons' => array($this->container->get('translator')->trans('security.login.remember_me', array(), $domain) => true),
		/*'validators' => array(
    new NotNullValidator('Cochez cette case si vous souhaitez continuer'),)*/
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

		/*$this->form->add(new SecurimageField(array(
			'name' => 'captcha_code',
			'control_label' => true,
			'icon_size' => 25,
			'input_text' => 'Entrez le text:',
			'validators' => array(
		new NotNullValidator('Remplissez le captcha pour pouvoir continuer'),
		new SecurimageValidator('The security code entered was incorrect'),
		)
		)));*/
	   /*
	   ->add(new \library\SelectField(array(
        'label' => 'Marque',
        'name' => 'Marque_ID',
		'options' => $arraySelectMarque,
		'validators' => array(
    new \library\NotNullValidator('Choisir la marque'),)
		)))
	   ->add(new \library\SelectField(array(
        'label' => 'State',
        'name' => 'State_ID',
		'options' => $arraySelectState,
		'validators' => array(
    new \library\NotNullValidator('Choisir l\'état'),)
		)))
	   ->add(new \library\TextareaField(array(
        'label' => 'Teaser de l\'article',
		'title' => 'Texte qui servira de teaser',
        'name' => 'TeaserTexte',
		'class' => 'tinymce',
		)))
	   ->add(new \library\TextareaField(array(
        'label' => 'Description de l\'article',
		'title' => 'Texte qui servira de description',
        'name' => 'Description',
		'placeholder' => 'Description ',
		'class' => 'tinymce',
		)))
	   ->add(new \library\InputField(array(
		'input_type' => 'number',
        'label' => 'Stock',
		'title' => 'le nombre de pièces en stock (entre 0 et 9999)',
        'name' => 'Stock',
		'min' => 0,
        'max' => 9999,
		'pattern' => "[0-9]{0,}",
		'placeholder' => $article['Stock']
		)))
	   ->add(new \library\InputField(array(
		'input_type' => 'number',
        'label' => 'Prix unitaire',
		'title' => 'Prix de l\'article (en euro), utiliser le point ( . ) pour la virgule',
        'name' => 'Prix',
        //'maxlength' => 7,
		'min' => 0,
        'max' => 9999.99,
		'step' => 0.01,
		'placeholder' => '0.00 ',
		'pattern' => "[,.0-9]{0,}",
		)))
	   ->add(new \library\InputField(array(
		'input_type' => 'url',
        'label' => 'Lien externe (URL)',
		'title' => 'lien vers site le officiel ou autre concernant l\'article',
        'name' => 'Url',
        'maxlength' => 255,
		'placeholder' => 'http://',
		)))
	   ->add(new \library\TextareaField(array(
        'label' => 'Meta Description',
		'title' => 'Texte qui servira de meta description',
        'name' => 'MetaArticle',
		'placeholder' => 'Meta Description',
		'class' => 'tinymce',
		)))
       ;*/
  }

  /*public function getEntityInstance()
  {
     return 'Acme\AcmeModule\Entity\User';
  }*/
}