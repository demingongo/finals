<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Rgs\CatalogModule\Entity\Model\PublishedInterface;
use Rgs\CatalogModule\Entity\Category,
	Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Advertisement,
	Rgs\CatalogModule\Entity\Brand,
	Rgs\CatalogModule\Entity\State;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Form\Validator as N_Form_Validator;

use Symfony\Component\Debug as Symfony_Debug;

use Utils\ToolFieldsUtils;
use Rgs\AdminModule\Util\AdminModuleUtils;

class AdminController extends \Novice\BackController
{
	
	/*******************************PRIVATE FUNCTIONS*************************************/

	private function processPostManagement(Request $request, $cm)
	{
		if($request->isMethod('POST'))
		{

			$addRouteId = $cm->getAddRouteId();
			$editRouteId = $cm->getEditRouteId();
			$repositoryName = $cm->getRepositoryName();
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);
				
				if($submit == "add.new"){
					return $this->redirect($this->generateUrl($addRouteId));
				}

				if($request->request->has('cid')){
					$ids = $request->request->get('cid');
					if($submit == "publish"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->publish($ids);
					}
					else if($submit == "unpublish"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->publish($ids, PublishedInterface::NOT_PUBLISHED);
					}
					else if($submit == "delete"){
						try{
							if($cm->getName() == 'category'){
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

	public function executeContentManager(Request $request)
	{
		$attributes = $request->attributes->all();

		//get content manager class from content_manager attribute 
		$contentManagerClass = $attributes['content_manager'];
		$cm = new $contentManagerClass($this->container);

		$r = $this->processPostManagement($request, $cm);
		if(is_object($r) && $r instanceof Response)
			return $r;
		
		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		$limit = 15;
		$ordering = $cm->getDefaultOrder();

		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$customFields = $cm->getCustomFields();

		$toolButtons = $cm->getToolsButtons();
		$toolButtons = isset($toolButtons) && is_array($toolButtons) ? $toolButtons : array();

		$orderingField = $fieldsUtils->createOrderField($cm->getOrderOptions());

		$limitField = $fieldsUtils->createLimitField();

		$newWhere = $cm->processCustomFields($request, $where, $customFields);

		$where = isset($newWhere) && is_array($newWhere) ? $newWhere : $where;

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

		list($sort, $order) = explode(" ",$ordering);

		$totalItems = $this->getDoctrine()->getManager()
			->getRepository($cm->getRepositoryName())
			->countItems($where);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		$items = $this->getDoctrine()->getManager()
			->getRepository($cm->getRepositoryName())
			->findItems($limit, $page, $where, array($sort => $order));

		$adminModuleUtils = new AdminModuleUtils();
		$columns = $cm->getColumns();

		$this->assign("columns", $columns);

		$this->assign("items", $items);

		$this->assign("title", $cm->getTitle());

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());

		foreach($customFields as $widgetName => $field){
			$customFields[$widgetName] = $field->buildWidget();
		}

		$this->assign("customWidgets", $customFields);

		foreach($toolButtons as $key => $field){
			$toolButtons[$key] = $field->buildWidget();
		}
		$this->assign("toolButtons", $toolButtons);

	}
	
	/*******************************CATEGORIES*************************************/
		

	public function executeEditCategory(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editCategory.php');

		if($request->attributes->has('id')){
			$category = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Category')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$category = new Category();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\CategoryFormBuilder($category))
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
				return $this->redirect($this->generateUrl('rgs_admin_gestion_category'));
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


		$form = $this->buildForm(new \Rgs\CatalogModule\Form\ArticleFormBuilder($article))
						 ->form();

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

	/*******************************ADVERTISEMENTS*************************************/

	public function executeEditAdvertisement(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editArticle.php');

		if($request->attributes->has('id')){
			$article = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Advertisement')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$article = new Advertisement();
		}


		$form = $this->buildForm(new \Rgs\CatalogModule\Form\AdvertisementFormBuilder($article))
						 ->form();

		$form->handleRequest($request);
		
		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			if ($form->isValid())
			{
				$em->persist($article);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_advertisement'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_advertisements_edit', array(
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
	
	
	/*******************************BRANDS*************************************/
	
	
	public function executeEditBrand(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editBrand.php');

		if($request->attributes->has('id')){
			$brand = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Brand')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$brand = new Brand();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\BrandFormBuilder($brand))
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
				$em->persist($brand);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_brand'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_brands_edit', array(
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
	
	
	/*******************************STATES*************************************/
	
	
	public function executeEditState(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Content/editState.php');

		if($request->attributes->has('id')){
			$state = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:State')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$state = new State();
		}

		try{
			$form = $this->buildForm(new \Rgs\CatalogModule\Form\StateFormBuilder($state))
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
				$em->persist($state);
				$em->flush();
				$em->getConnection()->commit();

				return $this->redirect($this->generateUrl('rgs_admin_gestion_state'));
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_states_edit', array(
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
	
}
