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

use Rgs\CatalogModule\Entity\Category;

class CategoryFormBuilder extends FormBuilder
{

	public function getName()
	{
		return 'category_form';
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
		//'addon' => '@', //'&euro;',
		/*'right_addon_class' => 'input-group-btn',
		'right_addon' => $dropdown2,//'<input type="checkbox" aria-label="...">',*/ //'<button class="btn btn-default" type="button">Go!</button>',
		//'attributes' => array('data-error' => 'something to write'),
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (64 caractères maximum)', 64),
    new NotNullValidator('Spécifiez le titre'),)
		)))
		->add(new TextareaField(array(
        'label' => $trans('form.description'),
        'name' => 'description',
		'control_label' => true,
		)))
		->add(new Prototype(array('name' => 'articles')))
		->add(new Prototype(array('name' => 'categories')))
		->add(new Prototype(array('name' => 'image')))
		->add(new RadioField(array(
        'label' => $trans('form.status'),
		'control_label' => true,
        'name' => 'published',
		'inline' => 1,
		'buttons' => array('Published' => Category::PUBLISHED , 'Unpublished' => Category::NOT_PUBLISHED),
		'validators' => array(
    new NotNullValidator('Choisir le statut'),)
		)))
		->add(new InputField(array(
        'label' => $trans('form.created_at'),
		'type' => 'text',
        'name' => 'created_at_to_string',
		'validation_states' => false,
		//'bootstrap' => false,
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
			'name' => 'image',
			'type' => 1,
			'label' => $trans('form.image'),
			//'input_attributes' => array("required" => "required"),
			'text_remove_btn' => '&times;',
			'akeys' => array(md5('one'), md5('gfk'), md5('theodore')),
		);

		$this->form->addExtension(new \Novice\Form\Extension\Filemanager\FilemanagerExtension('/plugins/filemanager/filemanager', $options));

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
		$this->form->addExtension(new \Novice\Form\Extension\Entity\EntityExtension($this->container->get('managers'), array(
		'class' => 'RgsCatalogModule:Article',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('a')
					->orderBy('a.name', 'ASC');
		},
        'label' => 'Articles',
        'name' => 'articles',
		'multiple' => true,
		'control_label' => true,
		'attributes' => 
			array(
				'data-placeholder' => 'Choisir un article',
				'style' => 'width: 95%',
				'class' => 'select2',
			),
		)));
		
		$options = array(
			'class' => 'RgsCatalogModule:Category',
			'label' => 'Parent Category',
			'control_label' => true,
			'name' => 'categories',
		    //'choice_label' => function($cat){return $cat->getName();},
			'attributes' => 
			array(
				'data-placeholder' => 'Choisir une catégorie mère',
				'data-theme' => 'classic',
				'data-allow-clear' => 'true',
				'style' => 'width: 95%',
				'class' => 'select2'
			),
			);
		$nsm = $this->container->get('nested_set')->getManager('RgsCatalogModule:Category');
		$this->form->addExtension(new \DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension($nsm,$options));
  }
}