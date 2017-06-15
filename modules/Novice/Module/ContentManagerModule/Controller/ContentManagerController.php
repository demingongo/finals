<?php
namespace Novice\Module\ContentManagerModule\Controller;

use Novice\BackController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Utils\ToolFieldsUtils;

class ContentManagerController extends BackController
{
	public function executeIndex(Request $request)
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

		$toolButtons = $cm->getToolButtonsGroup()->getToolButtons();
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
				if(is_null($v)){
					$qb->andWhere($qb->expr()->isNull($k));
				}
				else{
					$qb->andWhere($qb->expr()->eq($k, '?'.$i))
						->setParameter($i, $v);
					$i++;
				}
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
				if(is_null($v)){
					$qb2->andWhere($qb2->expr()->isNull($k));
				}
				else{
					$qb2->andWhere($qb2->expr()->eq($k, '?'.$i))
						->setParameter($i, $v);
					$i++;
				}
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

	private function processPostManagement(Request $request, $cm)
	{
		if($request->isMethod('POST'))
		{

			/*$addRouteId = $cm->getAddRouteId();
			$editRouteId = $cm->getEditRouteId();
			$repositoryName = $cm->getEntityName();*/
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);

				$response;
				$btns = $cm->getToolButtonsGroup()->getToolButtons();
				foreach($btns as $btn){
					$submitVal = $btn->value();
					if($submit == $submitVal){
						$ids = null;
						if($request->request->has('cid')){
							$ids = $request->request->get('cid');
						}
						//if it's not an item action and with no item selected
						if(!($btn->itemAction() && empty($ids))){
							$response = $btn->onSubmit($ids);
						}
						break;
					}
				}
				return $response;
				
				/*if($submit == "add.new"){
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
							$this->get('session')->getFlashBag()->set('error', $e->getMessage());
						}
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$ids[0])));
					}
				}*/
			}
		}
	}
}
