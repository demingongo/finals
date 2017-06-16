<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Rgs\CatalogModule\Entity\UserRequest;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Form\Field\InputField;

use Symfony\Component\Debug as Symfony_Debug;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Utils\ToolFieldsUtils;

use Novice\Module\SmartyBootstrapModule\Util\ItemProperty;

class RequestManagerController extends \Novice\BackController
{
	
	private function processPostGestion(Request $request, $itemType)
	{
		switch($itemType)
			{
				case 'userrequest':
					$addRouteId = '';
					$editRouteId = 'rgs_admin_requests_details';
					$repositoryName = 'RgsCatalogModule:UserRequest';
					break;
				default:
					throw new \InvalidArgumentException('The second argument in '.__METHOD__.' must be string: \'userrequest\' ');
					return;
			}
		if($request->isMethod('POST'))
		{
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);

				$em = $this->getDoctrine()->getManager();
				
				if($submit == "cancelAll"){
					$em->getConnection()->beginTransaction();
					try{
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)->findAll();
						foreach($result as $entity){
								$em->remove($entity);
						}
						$em->flush();
						$em->getConnection()->commit();

						$this->get('session')->getFlashBag()->set('success', 'Successfully deleted');
					}
					catch(\Exception $e){
						$em->close();
						$em->getConnection()->rollback();
						$this->get('session')->getFlashBag()->set('error', '<b>Failure occured</b>');
					}

					return;
				}

				if($request->request->has('cid')){
					$ids = $request->request->get('cid');
					$firstId = $ids[0];
					if($submit == "cancel"){
						$em->getConnection()->beginTransaction();
						try{
							foreach($ids as $id){
								$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
											->findOneById($id);
								$em->remove($result);
							}
							$em->flush();
							$em->getConnection()->commit();

							$this->get('session')->getFlashBag()->set('success', 'Successfully deleted');
						}
						catch(\Exception $e){
							$em->close();
							$em->getConnection()->rollback();
							$this->get('session')->getFlashBag()->set('error', '<b>Failure occured</b>');
						}
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$firstId, 'state' => $itemType)));
					}
					else if($submit == "valid"){
						$allRequests = array();
						$em->getConnection()->beginTransaction();
						try{
							foreach($ids as $id){
								$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
											->findOneById($id);
								if($result->isPublished()){
									continue;
								}
								$result->setPublished($result::PUBLISHED);
								$em->persist($result);
								$allRequests[] = $result;
							}
							$em->flush();
							$em->getConnection()->commit();

							$this->get('session')->getFlashBag()->set('success', 'Success');
						}
						catch(\Exception $e){
							$em->close();
							$em->getConnection()->rollback();
							$this->get('session')->getFlashBag()->set('error', '<b>Fail</b>');
							return;
						}
						foreach($allRequests as $r){
							$this->get('rgs.mailer')->sendRequestConfirm($r);
						}
						
					}
				}
			}
		}
	}
	
	public function executeGestionRequest(Request $request){
		
		$this->setView('file:[RgsAdminModule]Requests/gestionRequest.php');
		
		$r = $this->processPostGestion($request, 'userrequest');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		$search = "";
		$limit = 15;
		$ordering = array("r.createdAt" => "DESC");
		$orderingString = "r.createdAt DESC";

		$where = array();
		
		$searchField = new InputField(array(
			'name' => 'search',
			'placeholder' => 'Search login',
			'feedback' => false,
		
		));

		$orderingField = new SelectField(array(
			'name' => 'ordering',
			'empty_option' => false,
			'options' => array( 
				"r.createdAt DESC" => "Date descending",
				"r.createdAt ASC" => "Date ascending",
				"u.login ASC" => "User ascending",
				"u.login DESC" => "User descending",
			),
			'feedback' => false,
			'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'Order by',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$limitField = new SelectField(array(
			'name' => 'limit',
			'empty_option' => false,
			//'bootstrap' => false,
			'options' => array( 
				2 => '2',
				5 => '5',
				10 => '10',
				15 => '15',
				20 => '20',
				25 => '25',
				30 => '30',
				50 => '50'),
			'feedback' => false,
			'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'Number per page',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
		
		if($request->request->has('search')){
			$req_search = $request->request->get('search');
			if(!empty($req_search)){
					$search = $req_search;
			}
		}

		if($request->request->has('ordering')){
			$req_ordering = $request->request->get('ordering');
			if(!empty($req_ordering)){
				list($sort, $order) = explode(" ",$req_ordering);
				$ordering = array($sort => $order);
				if(!isset($ordering["r.createdAt"])){
					$ordering["r.createdAt"] = "DESC";
				}
				$orderingString	= $req_ordering;
			}
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
		}
		
		
		$searchClosure = function($qb) use ($search){
			if(!empty($search)){
				$qb->andWhere($qb->expr()->orX('u.login LIKE :login', 'u.email LIKE :login'))
					->setParameter('login', '%'.$search.'%');
			
			}
			return $qb;
		};

		$qb = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:UserRequest')
			->getCountRequestsQB($where);

		$qb = $searchClosure($qb);

		$totalItems = $qb->getQuery()->getSingleScalarResult();

		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;
			
		$qb = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:UserRequest')
			->getFindRequestsQB($limit, $page, $where, $ordering);

		$qb = $searchClosure($qb);

		$requests = new Paginator($qb);

		$this->assign("requests", $requests);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($orderingString)->buildWidget());
		
		$this->assign("searchWidget", $searchField->setValue($search)->buildWidget());
	}
	
	
	public function executeDetailsRequest(Request $request)
	{	
		$item = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:UserRequest')->findOneById($request->attributes->get('id'));

		if(empty($item)){
			return $this->redirectError();
		}

		if($request->isMethod("POST") && !$item->hasStatus()){
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);

				$saveit = false;
				if($submit == "accept"){
					$item->setStatus(true);
					$saveit = true;
				}
				else if ($submit == "decline"){
					$item->setStatus(false);
					$saveit = true;
				}

				if($saveit){
					$em = $this->getDoctrine()->getManager();
					$em->getConnection()->beginTransaction();
					try{
						$em->persist($item);
						$em->flush();
						$em->getConnection()->commit();
						$this->get('session')->getFlashBag()->set('success', 'Successfully updated');
					
						$this->get('rgs.mailer')->sendRequestConfirm($item);
					}
					catch(\Exception $e){
						$em->close();
						$em->getConnection()->rollback();
						$this->get('session')->getFlashBag()->set('error', '<b>Failure occured</b>');
					}
				}
				

			}

		}
		
		$this->assign("item", $item);

	}
	
}
