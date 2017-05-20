<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Rgs\CatalogModule\Entity\Model\PublishedInterface;
use Rgs\CatalogModule\Entity\Categorie,
	Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Marque,
	Rgs\CatalogModule\Entity\Etat;

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
		$allVisible = 7;
		$visibility = $allVisible;
		$where = array();

		$fieldsUtils = new ToolFieldsUtils();

		$customFields = $cm->getCustomFields();

		$toolButtons = $cm->getToolsButtons();
		$toolButtons = isset($toolButtons) && is_array($toolButtons) ? $toolButtons : array();

		$visibilityField = $fieldsUtils->createVisibilityField(array(
				$allVisible => "All",
				PublishedInterface::PUBLISHED => "published",
				PublishedInterface::NOT_PUBLISHED => "not published",
			));

		$orderingField = $fieldsUtils->createOrderField($cm->getOrderOptions());

		$limitField = $fieldsUtils->createLimitField();

		$newWhere = $cm->processCustomFields($request, $where, $customFields);

		$where = isset($newWhere) && is_array($newWhere) ? $newWhere : $where;
		
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
			$where[$cm->getVisibilityKey()] = (bool) $visibility;
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
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());

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
	
}
