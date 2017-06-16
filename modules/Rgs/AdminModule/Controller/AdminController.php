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
			$repositoryName = $cm->getEntityName();
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

		$entityName = $cm->getEntityName();
		$alias = $cm->getAlias();

		$em = $this->getDoctrine()->getManager();

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

		$repository = $em->getRepository($cm->getEntityName());

		$repositoryObject = new \ReflectionObject($repository);

		$totalItems;
		if($repositoryObject->hasMethod('countItems')){
			$totalItems = $repository->countItems($where);
		}
		else{
			$qb = $em->createQueryBuilder()
            ->select($alias)
            ->from($cm->getEntityName(), $alias, null);

			$qb->select('count('.$alias.'.id)');
			$i = 1;
			foreach($where as $k => $v){
				$qb->andWhere($qb->expr()->eq($k, '?'.$i))
					->setParameter($i, $v);
				$i++;
			}
			$totalItems = $qb->getQuery()->getSingleScalarResult();
		}

		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;
		
		$items = [];
		if($repositoryObject->hasMethod('findItems')){
			$items = $repository->findItems($limit, $page, $where, array($sort => $order));
		}
		else{
			$qb2 = $em->createQueryBuilder()
            ->select($alias)
            ->from($cm->getEntityName(), $alias, null);
		
			$i = 1;
			foreach($where as $k => $v){
				$qb2->andWhere($qb2->expr()->eq($k, '?'.$i))
					->setParameter($i, $v);
				$i++;
			}
			
			$qb2->addOrderBy($sort, $order);
		
			$qb2->setFirstResult(($page-1) * $limit)
				->setMaxResults($limit);

			$items = new \Doctrine\ORM\Tools\Pagination\Paginator($qb2);
		}
		
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

		$session = $this->get('session');

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
					if($e instanceof \Doctrine\DBAL\Exception\UniqueConstraintViolationException){
						$form->getField('name')->setWarningMessage(': "'.$category->getName().'" already exists');
						$session->getFlashBag()->set('error', 'Error');
					}
					else
						throw $e;
			}
		}

		$this->assign(array('title' => 'Category',
							'form' => $form->createView()));
	}
	
	/*******************************ARTICLES*************************************/
	
	public function executeEditArticle(Request $request)
	{
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

		$this->assign(array('title' => 'Article',
							'form' => $form->createView()));
	}

	/*******************************ADVERTISEMENTS*************************************/

	public function executeEditAdvertisement(Request $request)
	{
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

		$this->assign(array('title' => 'Advertisement',
							'form' => $form->createView()));
	}
	
	
	/*******************************BRANDS*************************************/
	
	
	public function executeEditBrand(Request $request)
	{
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

		$this->assign(array('title' => 'Brand',
							'form' => $form->createView()));
	}
	
	
	/*******************************STATES*************************************/
	
	
	public function executeEditState(Request $request)
	{

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

		$this->assign(array('title' => 'State',
							'form' => $form->createView()));
	}
	
}
