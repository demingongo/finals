<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rgs\CatalogModule\Entity\Categorie,
	Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Marque,
	Rgs\CatalogModule\Entity\Etat;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Form\Validator as N_Form_Validator;

use Symfony\Component\Debug as Symfony_Debug;

use Novice\Module\SmartyBootstrapModule\Util\ItemProperty;

use Utils\ToolFieldsUtils;

class AdminController extends \Novice\BackController
{
	
	/*******************************PRIVATE FUNCTIONS*************************************/
		
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
				    $addRouteId = 'rgs_admin_etats_add';
					$editRouteId = 'rgs_admin_etats_edit_2';
					$repositoryName = 'RgsCatalogModule:Etat';
					break;
				case 'marque':
					$addRouteId = 'rgs_admin_marques_add';
					$editRouteId = 'rgs_admin_marques_edit_2';
					$repositoryName = 'RgsCatalogModule:Marque';
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
						try{
							if($itemType == 'categorie'){
								$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
								->deleteByIds($this->get('nested_set'), $ids);
							}
							else{
								$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
								->deleteByIds($ids);
							}
						}
						catch(\Exception $e){
							$this->get('session')->getFlashBag()->set('error', '<b>ForeignKeyConstraintViolationException</b> occured, could not delete');
						}
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$ids[0])));
					}
				}
			}
		}
	}
	
	/********************************************************************/
	
	
	
	public function executeIndex(Request $request)
	{	
		$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}
	
	
	/*******************************MEDIA*************************************/
	

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
	}
	
	
	
	/*******************************CATEGORIES*************************************/
		

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
					$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_categories_edit', array(), true).
				'" class="alert-link">fill in the form</a> and try submitting again.');
					break;
				default:
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
		
		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		$limit = 15;
		$ordering = "c.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;
		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$visibilityField = $fieldsUtils->createVisibilityField(array(
				$allVisible => "All",
				Categorie::PUBLISHED => "published",
				Categorie::NOT_PUBLISHED => "not published",
			));

		$orderingField = $fieldsUtils->createVisibilityField(array( 
				"c.name ASC" => "Title ascending",
				"c.name DESC" => "Title descending",
				"c.id ASC" => "Id ascending",
				"c.id DESC" => "Id descending",
			));

		$limitField = $fieldsUtils->createLimitField();

		
		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($visibility != $allVisible){
			$where['c.published'] = (bool) $visibility;
		}

		list($sort, $order) = explode(" ",$ordering);

		$totalItems = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Categorie')
			->countCategories($where);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		$categories = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Categorie')
			->findCategories($limit, $page, $where, array($sort => $order));


		$this->assign("categories", $categories);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());
	}
	
	
	/*******************************ARTICLES*************************************/
	
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


	public function executeGestionArticle(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionArticle.php');

		$r = $this->processPostGestion($request, 'article');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;


		$limit = 15;
		$ordering = "a.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;
		$byCategorie = null;

		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$categoryField = $fieldsUtils->createCategoryEntityField($this->getDoctrine());

		$visibilityField = $fieldsUtils->createVisibilityField(array(
				$allVisible => "All",
				Article::PUBLISHED => "published",
				Article::NOT_PUBLISHED => "not published",
			));

		$orderingField = $fieldsUtils->createOrderField(array( 
				"a.name ASC" => "Title ascending",
				"a.name DESC" => "Title descending",
				"a.id ASC" => "Id ascending",
				"a.id DESC" => "Id descending",
			));

		$limitField = $fieldsUtils->createLimitField();

		if($request->request->has('categorie')){
			$req_byCategorie = $request->request->get('categorie');
			if(!empty($req_byCategorie)){
				$byCategorie = $req_byCategorie;
				$where['a.categorie'] = $byCategorie;
			}
		}

		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($visibility != $allVisible){
			$where['a.published'] = (bool) $visibility;
		}

		list($sort, $order) = explode(" ",$ordering);

		$totalItems = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Article')
			->countArticles($where);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		$items = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Article')
			->findArticles($limit, $page, $where, array($sort => $order));
	
		$columns = array(
			[
				'property' => 'published',
				'label' => 'Status',
				'filter' => function($propertyValue, $entity, $i, $smarty){
					if($entity->isPublished()){
						$publishValue='unpublish';
						$src = $smarty->getAssets()->getUrl('/img/pictos/Ok-16.png', null);
					}
					else{
						$publishValue='publish';
						$src = $smarty->getAssets()->getUrl('/img/pictos/Cancel_2-16.png', null);
					}

					$result = '';
					$result .= '<input type="image"';
					$result .= 'src="'.$src.'"';
					$result .= 'class="btn btn-outline btn-default" name="submit[]"';
					$result .= 'onclick="formTache(\''.$publishValue.'\',\'cb'.$i.'\')"';
					$result .= 'value="'.$publishValue.'" />';

					return $result;
				}
			],
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_articles_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			],
			[
				'property' => 'categorie.name',
				'label' => 'Category',
				'class' => 'hidden-xs',
				'route' => [
					'id' => 'rgs_admin_categories_edit',
					'params' =>[
						'id' => new ItemProperty('categorie.id'), 
						'slug' => new ItemProperty('categorie.slug')
					],
					'absolute' => true
				]
			]
		);

		$this->assign("columns", $columns);

		$this->assign("items", $items);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());

		$this->assign("catWidget", $categoryField->setValue($byCategorie)->buildWidget());
	}
	
	
	/*******************************MARQUES*************************************/
	
	
	public function executeEditMarque(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editMarque.php');

		if($request->attributes->has('id')){
			$marque = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Marque')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$marque = new Marque();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\MarqueFormBuilder($marque))
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
		
		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			if ($form->isValid())
			{
				$em->persist($marque);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_marque'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_marques_edit', array(
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
	
	
	
	public function executeGestionMarque(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionMarque.php');

		$r = $this->processPostGestion($request, 'marque');
		if(is_object($r) && $r instanceof Response)
			return $r;
		
		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		$limit = 15;
		$ordering = "m.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;

		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$visibilityField = $fieldsUtils->createVisibilityField(array(
				$allVisible => "All",
				Marque::PUBLISHED => "published",
				Marque::NOT_PUBLISHED => "not published",
			));

		$orderingField = $fieldsUtils->createOrderField(array( 
				"m.name ASC" => "Title ascending",
				"m.name DESC" => "Title descending",
				"m.id ASC" => "Id ascending",
				"m.id DESC" => "Id descending",
			));

		$limitField = $fieldsUtils->createLimitField();

		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($visibility != $allVisible){
			$where['m.published'] = (bool) $visibility;
		}

		list($sort, $order) = explode(" ",$ordering);

		$totalItems = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Marque')
			->countMarques($where);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		$marques = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Marque')
			->findMarques($limit, $page, $where, array($sort => $order));


		$this->assign("marques", $marques);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());
	}
	
	
	
	/*******************************ETATS*************************************/
	
	
	public function executeEditEtat(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editEtat.php');

		if($request->attributes->has('id')){
			$etat = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Etat')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$etat = new Etat();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\EtatFormBuilder($etat))
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
		
		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			if ($form->isValid())
			{
				$em->persist($etat);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_etat'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_etats_edit', array(
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
	
	
	
	public function executeGestionEtat(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionEtat.php');

		$r = $this->processPostGestion($request, 'etat');
		if(is_object($r) && $r instanceof Response)
			return $r;
		
		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		$limit = 15;
		$ordering = "e.name ASC";
		$allVisible = 7;
		$visibility = $allVisible;

		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$visibilityField = $fieldsUtils->createVisibilityField(array(
				$allVisible => "All",
				Etat::PUBLISHED => "published",
				Etat::NOT_PUBLISHED => "not published",
			));

		$orderingField = $fieldsUtils->createOrderField(array( 
				"e.name ASC" => "Title ascending",
				"e.name DESC" => "Title descending",
				"e.id ASC" => "Id ascending",
				"e.id DESC" => "Id descending",
			));

		$limitField = $fieldsUtils->createLimitField();

		if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering))
				$ordering = $req_ordering;
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}

		if($visibility != $allVisible){
			$where['e.published'] = (bool) $visibility;
		}

		list($sort, $order) = explode(" ",$ordering);

		$totalItems = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Etat')
			->countEtats($where);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		$etats = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Etat')
			->findEtats($limit, $page, $where, array($sort => $order));

		$this->assign("etats", $etats);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());
	}
	
}
