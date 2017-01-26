<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rgs\CatalogModule\Entity\Categorie;
use Rgs\CatalogModule\Entity\Article;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Form\Validator as N_Form_Validator;

use Symfony\Component\Debug as Symfony_Debug;

class AdminController extends \Novice\BackController
{
	public function executeIndex(Request $request)
	{	
		$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}

	public function executeGestionMedia(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionMedia.php');

		$options = array(
			'filemanager_path' => '/plugins/filemanager/filemanager',
			'base_url' => $request->getBaseUrl(),

			'akey' => md5('one'),
		);

		$path = \Novice\Form\Extension\Filemanager\Filemanager::getPath($options);


		$this->assign("filemanagerPath", $path);

		//return $this->redirect($path);
	}

	public function executeEditCategorie(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editCategorie.php');

		if($request->attributes->has('id')){
			$categorie = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Categorie')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$categorie = new Categorie();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\CategorieFormBuilder($categorie))
						 ->form();
		}
		catch(\Exception $e){
			if($e->getCode() == E_RECOVERABLE_ERROR || $e instanceof Symfony_Debug\Exception\ContextErrorException){
				return $this->redirectError('404');
			}
			else{
				throw $e;
			}
		}

		$form->handleRequest($request);
		try{
			if ($form->isValid())
			{
				$form->execute();
				return $this->redirect($this->generateUrl('rgs_admin_gestion_categorie'));
			}
		}
		catch(\Exception $e){ //\Novice\Form\Exception\SecurityException
			switch($e->getCode()){
				case EntityNodeExtension::PARADOX :
					$form->getField('categories')->setWarningMessage("La catégorie n'a pas pu être déplacée car elle ne peut être son ancêtre et son descendant");
					break;
				case \Novice\Form\Exception\SecurityException::SECURITY_EXCEPTION :
					$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('user_security_login', array(), true).
				'" class="alert-link">fill in the form</a> and try submitting again.');
					break;
				default:
					throw $e;
			}
		}

		$this->assign(array('title' => 'Edit',
							'form' => $form->createView()));
	}

	public function executeEditArticle(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editArticle.php');

		if($request->attributes->has('id')){
			$article = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Article')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$article = new Article();
		}

		//try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\ArticleFormBuilder($article))
						 ->form();
		//}
		//catch(\Exception $e){
		//	return $this->redirectError('404');
		//}

		$form->handleRequest($request);
		
		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			if ($form->isValid())
			{
				$em->persist($article);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_article'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_articles_edit', array(
					"id" => $article->getId(),
					"slug" => $article->getSlug(),
				), 
				true).'" class="alert-link">fill in the form</a> and try submitting again.');
			}
			else{
				throw $e;
			}
		}

		$this->assign(array('title' => 'Edit',
							'form' => $form->createView()));
	}

	public function executeGestionCategorie(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionCategorie.php');

		$r = $this->processPostGestion($request, 'categorie');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = 1;
		$limit = 15;
		$ordering = "c.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;

		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}

		list($orderBy, $ascending) = explode(" ",$ordering);

		$published = null;
		if($visibility != $allVisible)
			$published = $visibility;

		$categories = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Categorie')
			//->findBy(array(), array('name' => 'ASC'));
			->getCategories($limit, $page, $orderBy, $ascending, $published);

		$this->assign("categories", $categories);

		$this->assign("limitOptions", array( 2, 5, 10, 15, 20, 25, 30, 50));
		$this->assign("limit", $limit);
		$this->assign("orderingOptions", 
			array( 
			"c.name ASC" => "Title ascending",
			"c.name DESC" => "Title descending",
			"c.id ASC" => "Id ascending",
			"c.id DESC" => "Id descending",
			)
		);
		$this->assign("ordering", $ordering);
		$this->assign("visibilityOptions", 
			array( 
			$allVisible => "All",
			Categorie::PUBLISHED => "published",
			Categorie::NOT_PUBLISHED => "not published",
			)
		);
		$this->assign("visibility", $visibility);
	}

	public function executeGestionArticle(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionArticle.php');

		//dump($request->request);

		$entityExt = new \Novice\Form\Extension\Entity\EntityExtension($this->getDoctrine(), array(
		'class' => 'RgsCatalogModule:Categorie',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->orderBy('c.name', 'ASC');
		},
        'name' => 'categorie',
		'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'All Categories',
			'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$r = $this->processPostGestion($request, 'article');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = 1;
		$limit = 15;
		$ordering = "a.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;
		$byCategorie = null;

		$where = array();

		$visibilityField = new SelectField(array(
			'name' => 'visibility',
			'empty_option' => false,
			'empty_option_value' => $allVisible,
			'empty_option_text' => 'All',
			'options' => array(
				$allVisible => "All",
				Article::PUBLISHED => "published",
				Article::NOT_PUBLISHED => "not published",
			),
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'All',
			'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$orderingField = new SelectField(array(
			'name' => 'ordering',
			'empty_option' => false,
			'options' => array( 
				"a.name ASC" => "Title ascending",
				"a.name DESC" => "Title descending",
				"a.id ASC" => "Id ascending",
				"a.id DESC" => "Id descending",
			),
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'Order by',
			'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$limitField = new SelectField(array(
			'name' => 'limit',
			'empty_option' => false,
			'options' => array( 
				2 => '2',
				5 => '5',
				10 => '10',
				15 => '15',
				20 => '20',
				25 => '25',
				30 => '30',
				50 => '50'),
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'Number per page',
			'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}

		if($request->request->has('categorie')){
			$req_byCategorie = $request->request->get('categorie');
			if(!empty($req_byCategorie)){
				$byCategorie = $req_byCategorie;
				$where['a.categorie'] = $byCategorie;
			}
		}

		list($orderBy, $ascending) = explode(" ",$ordering);

		$published = null;
		if($visibility != $allVisible){
			$published = $visibility;
			$where['a.published'] = (bool) $visibility;
		}

		$items = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Article')
			//->findBy(array(), array('name' => 'ASC'));
			->findArticles($limit, $page, $where, array($orderBy => $ascending));

		$this->assign("items", $items);

		/*$this->assign("limitOptions", array( 2, 5, 10, 15, 20, 25, 30, 50));
		$this->assign("limit", $limit);*/
		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		/*$this->assign("orderingOptions", 
			array( 
			"a.name ASC" => "Title ascending",
			"a.name DESC" => "Title descending",
			"a.id ASC" => "Id ascending",
			"a.id DESC" => "Id descending",
			)
		);
		$this->assign("ordering", $ordering);*/
		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());
		
		/*$this->assign("visibilityOptions", 
			array( 
			$allVisible => "All",
			Article::PUBLISHED => "published",
			Article::NOT_PUBLISHED => "not published",
			)
		);
		$this->assign("visibility", $visibility);*/
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());

		$this->assign("catWidget", $entityExt->createField()->setValue($byCategorie)->buildWidget());
	}

	private function processPostGestion(Request $request, $itemType)
	{
		if($request->isMethod('POST'))
		{
			switch($itemType)
			{
				case 'article':
					$addRouteId = 'rgs_admin_articles_add';
					$editRouteId = 'rgs_admin_articles_edit_2';
					$repositoryName = 'RgsCatalogModule:Article';
					break;
				case 'categorie':
					$addRouteId = 'rgs_admin_categories_add';
					$editRouteId = 'rgs_admin_categories_edit_2';
					$repositoryName = 'RgsCatalogModule:Categorie';
					break;
				case 'etat':
					break;
				case 'marque':
					break;
				default:
					throw new \InvalidArgumentException('The second argument in '.__METHOD__.' must be string: \'article\' \'categorie\' \'etat\' or \'marque\'');
					return;
			}
			//dump($request->request->all());
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);
				
				if($submit == "add.new"){
					return $this->redirect($this->generateUrl($addRouteId));
				}

				//exit($submit);

				if($request->request->has('cid')){
					$ids = $request->request->get('cid');
					if($submit == "publish"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->publish($ids);
					}
					else if($submit == "unpublish"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->publish($ids, Categorie::NOT_PUBLISHED);
					}
					else if($submit == "delete"){
						if($itemType == 'categorie'){
							$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
							->deleteByIds($this->get('nested_set'), $ids);
						}
						else{
							$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
							->deleteByIds($ids);
						}
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$ids[0])));
					}
				}
			}
				//dump($request->request->all());
				//dump($request->request->get('cid'));
			//exit();
		}
	}
}
