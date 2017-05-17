<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Reservation;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Form\Field\InputField;
use Novice\Form\Validator as N_Form_Validator;

use Symfony\Component\Debug as Symfony_Debug;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Utils\ToolFieldsUtils;

class ReservationManagerController extends \Novice\BackController
{
	
	private function processPostGestion(Request $request, $itemType)
	{
		switch($itemType)
			{
				case 'expired':
					$addRouteId = '';
					$editRouteId = 'rgs_admin_reservations_details';
					$repositoryName = 'RgsCatalogModule:Reservation';
					$cancelAllFn = 'cancelExpired';
					break;
				case 'reservations':
					$addRouteId = '';
					$editRouteId = 'rgs_admin_reservations_details';
					$repositoryName = 'RgsCatalogModule:Reservation';
					$cancelAllFn = 'cancelNonExpired';
					break;
				default:
					throw new \InvalidArgumentException('The second argument in '.__METHOD__.' must be string: \'expired\' or \'reservations\'');
					return;
			}
		if($request->isMethod('POST'))
		{
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);
				
				if($submit == "cancelAll"){
					return $result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->$cancelAllFn();
				}

				if($request->request->has('cid')){
					$ids = $request->request->get('cid');
					$firstId = $ids[0];
					if($submit == "cancel"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->cancelByIds($ids);
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$firstId, 'state' => $itemType)));
					}
				}
			}
		}
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
	
	public function executeGestionReservation(Request $request){
		
		$state = $request->attributes->get('state') == 'expired' ? 'expired' : 'reservations';
		$expiredOnes = $state == 'expired';
		
		$this->setView('file:[RgsAdminModule]Reservations/gestionReservation.php');

		$fieldsUtils = new ToolFieldsUtils();
		
		$r = $this->processPostGestion($request, $state);
		if(is_object($r) && $r instanceof Response)
			return $r;
		
		// get page number
		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;

		// default filter and sort values
		$search = "";
		$limit = 15;
		$ordering = array("r.createdAt" => "DESC");
		$orderingString = "r.createdAt DESC";
		$where = array();
		
		//-- create filter & sort fields
		$searchField = $fieldsUtils->createSearchField(
			'Search (login or email)'
		);

		$limitField = $fieldsUtils->createLimitField();

		$orderingField = $fieldsUtils->createOrderField(array( 
				"r.createdAt DESC" => "Date descending",
				"r.createdAt ASC" => "Date ascending",
				"u.login ASC" => "User ascending",
				"u.login DESC" => "User descending",
		));
		//-- create filter & sort fields --END
		
		//-- process POST request filter & sort
		if($request->request->has('search')){
			$req_search = $request->request->get('search');
			if(!empty($req_search)){
					$search = $req_search;
			}
		}
		
		if($request->request->has('limit')){
			$req_limit = $request->request->get('limit');
			if(!empty($req_limit))
				$limit = $req_limit;
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
		//-- process POST request filter & sort --END
		
		//-- qb to show expired reservations or not
		if($expiredOnes){
			$expiredClosure = function($qb){
			$dt = new \Datetime("now");
			$qb->andWhere('r.expiresAt <= :expiresAt')
				->setParameter('expiresAt', $dt->format('Y-m-d H:i:s'));
			return $qb;
			};
		}
		else{
			$expiredClosure = function($qb){
			$dt = new \Datetime("now");
			$qb->andWhere('r.expiresAt > :expiresAt')
				->setParameter('expiresAt', $dt->format('Y-m-d H:i:s'));
			return $qb;
			};
		}
		//-- qb to show expired reservations or not --END
		
		//-- qb to search
		$searchClosure = function($qb) use ($search){
			if(!empty($search)){
				$qb->andWhere($qb->expr()->orX('u.login LIKE :login', 'u.email LIKE :login'))
					->setParameter('login', '%'.$search.'%');
			
			}
			return $qb;
		};

		//build the query WITHOUT limit filter
		$qb = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Reservation')
			->getCountReservationsQB($where);
		$qb = $expiredClosure($qb);	
		$qb = $searchClosure($qb);

		//get total of items
		$totalItems = $qb->getQuery()->getSingleScalarResult();

		//get number of pages and correct page number in case
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;
		
		//build the query WITH filters
		$qb = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Reservation')
			->getFindReservationsQB($limit, $page, $where, $ordering);
		$qb = $expiredClosure($qb);
		$qb = $searchClosure($qb);

		//get result from Paginator
		$reservations = new Paginator($qb);
		
		$this->assign("expiredPage", $expiredOnes);
		
		$this->assign("state", $state);

		$this->assign("reservations", $reservations);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($orderingString)->buildWidget());
		
		$this->assign("searchWidget", $searchField->setValue($search)->buildWidget());
	}
	
	
	public function executeDetailsReservation(Request $request)
	{
		$routeId = 'rgs_admin_gestion_reservations';
		if($request->attributes->has('state') && $request->attributes->get('state') == 'expired'){
			$routeId = 'rgs_admin_gestion_expired_reservations';
		}
		
		$this->setView('file:[RgsAdminModule]Reservations/detailsReservation.php');

		$em = $this->getDoctrine()->getManager();

		$reservation = $em->getRepository('RgsCatalogModule:Reservation')->findOneById($request->attributes->get('id'));

		if(!$reservation){
			return $this->redirect($this->generateUrl($routeId));
		}
		
		if($request->isMethod('POST'))
		{
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);


				if($submit == "cancel"){
						$result = $em->getRepository('RgsCatalogModule:Reservation')
						->cancelOneById($reservation->getId());
						return $this->redirect($this->generateUrl($routeId));
				}
				else if($submit == "valid"){
					$em->getConnection()->beginTransaction();
					try{
						$em->remove($reservation);
						$em->flush();
						$em->getConnection()->commit();

						return $this->redirect($this->generateUrl($routeId));
					}
					catch(\Exception $e){
						$em->close();
						$em->getConnection()->rollback();

						$this->get('session')->getFlashBag()->set('error', 'An error occured');
					}
				}
			}
		}		
		$this->assign("reservation", $reservation);
	}
	
}
