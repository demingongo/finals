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
use Rgs\CatalogModule\Entity\Article;
class ArticleFormBuilder extends FormBuilder
{
	public function getName()
	{
		return 'article_form';
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
		'attributes' => array(
			'data-error' => 'something to write',
			'title' => 'Choisir un titre',
		),
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (64 caractères maximum)', 64),
    new NotNullValidator('Spécifiez le titre'),)
		)))
		->add(new TextareaField(array(
        'label' => $trans('form.description'),
        'name' => 'description',
		'control_label' => true,
		)))
		->add(new Prototype(array('name' => 'category')))
		->add(new Prototype(array('name' => 'etat')))
		->add(new Prototype(array('name' => 'brand')))
		->add(new Prototype(array('name' => 'image')))
		->add(new InputField(array(
		'type' => 'number',
        'label' => 'Stock',
		'title' => 'le nombre de pièces en stock (entre 0 et 9999)',
        'name' => 'stock',
		'min' => 0,
        'max' => 9999,
		'pattern' => "[0-9]{0,}",
		'placeholder' => $this->form->entity()->getStock(),
		)))
	   ->add(new InputField(array(
		'type' => 'number',
        'label' => 'Prix unitaire',
		'title' => 'Prix de l\'article (en euro), utiliser le point ( . ) pour la virgule',
        'name' => 'prix',
		'min' => 0,
        'max' => 9999.99,
		'step' => 0.01,
		'placeholder' => '0.00 ',
		'addon' => '&euro;',
		'pattern' => "[,.0-9]{0,}",
		)))
		->add(new RadioField(array(
        'label' => $trans('form.status'),
		'control_label' => true,
        'name' => 'published',
		 'attributes' => array('class' => 'icheck iradio_line-red'),
		'inline' => 1,
		'required' => true,
		'buttons' => array('Published' => Article::PUBLISHED , 'Unpublished' => Article::NOT_PUBLISHED),
		'validators' => array(
    new NotNullValidator('Choisir le statut'),)
		)))
		->add(new InputField(array(
		'type' => 'url',
        'label' => 'Lien externe (URL)',
		'title' => 'lien vers site le officiel ou autres informations concernant l\'article',
        'name' => 'url',
        'maxlength' => 255,
		'placeholder' => 'http://',
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
		$this->form->addExtension(new \Novice\Form\Extension\Entity\EntityExtension($this->container->get('managers'), array(
		'class' => 'RgsCatalogModule:Brand',
		'choice_label' => 'name',
        'label' => 'Brand',
        'name' => 'brand',
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
		//'options' => $this->getBrands(),
		)));
		$this->form->addExtension(new \Novice\Form\Extension\Entity\EntityExtension($this->container->get('managers'), array(
		'class' => 'RgsCatalogModule:Etat',
		'choice_label' => 'name',
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('e')
					->orderBy('e.name', 'ASC');
		},
		'element_type' => 'SELECT_FIELD',
        'label' => 'Etat',
        'name' => 'etat',
		'required' => true,
		'control_label' => true,
		'attributes' => 
			array(
				'style' => 'width: 99%',
				'data-placeholder' => 'Choisir un état',
				//'data-theme' => 'classic',
				'data-allow-clear' => 'true',
				'data-minimum-results-for-search' => 15,
				'class' => 'select2',
				//'onchange' => 'this.form.submit()',
			),
		'validators' => array(
    new NotNullValidator('Choisir l\'état'),)
		)));
		$nsm = $this->container->get('nested_set')->getManager('RgsCatalogModule:Category');
		$options = array(
			'class' => 'RgsCatalogModule:Category',
			'label' => 'Category',
			'control_label' => true,
			'name' => 'category',
			'required' => true,
		    'choice_label' => function($cat){return $cat->getName();},
			'validators' => array(
    new NotNullValidator('Choisir la catégorie'),),
			'attributes' => 
			array(
				'style' => 'width: 99%',
				'data-placeholder' => 'Choisir une catégorie',
				'data-theme' => 'bootstrap',
				'data-allow-clear' => 'true',
				'data-minimum-results-for-search' => 15,
				'class' => 'select2',//'chosen-select-deselect',
				//'onchange' => 'this.form.submit()',
			),
			);
		$this->form->addExtension(new \DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension($nsm,$options));
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
  }
  private function getCats()
	{
		$retour = array();
		$em = $this->container->get('managers')->getManager();
		$repository = $em->getRepository('RgsCatalogModule:Category');
		$cs = $repository->findBy(
				array(),
				array('name' => 'ASC'),
				null,
				null
			);
		foreach($cs as $c){
			$retour[$c->getId()] = $c->getName();
		}
		
		return $retour;
		dump($cs);
		dump($retour);
		exit(__METHOD__);
	}
	private function getBrands()
	{
		$retour = array();
		$em = $this->container->get('managers')->getManager();
		$repository = $em->getRepository('RgsCatalogModule:Brand');
		$cs = $repository->findBy(
				array(),
				array('name' => 'ASC'),
				null,
				null
			);
		foreach($cs as $c){
			$retour[$c->getId()] = $c->getName();
		}
		
		return $retour;
	}
	private function getEtats()
	{
		$retour = array();
		$em = $this->container->get('managers')->getManager();
		$repository = $em->getRepository('RgsCatalogModule:Etat');
		$cs = $repository->findBy(
				array(),
				array('name' => 'ASC'),
				null,
				null
			);
		foreach($cs as $c){
			$retour[$c->getId()] = $c->getName();
		}
		
		return $retour;
	}
}