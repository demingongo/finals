<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Util\StringUtils;
use Rgs\UserModule\Entity\User;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Password;

use Symfony\Component\Debug as Symfony_Debug;

use Doctrine\ORM\Tools\Pagination\Paginator;

class UserManagerController extends \Novice\BackController
{
	public function executeGestionUser(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Users/gestionUser.php');

		$session = $this->get('session');

		//dump($request->request);

		$r = $this->processPostGestion($request, 'user');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = null;
		if($request->request->has('page'))
			$page = $request->request->get('page');
		if(!is_numeric($page))
			$page = 1;


		$limit = 15;
		$ordering = "u.login ASC";
		$allVisible = 7;
		$visibility = $allVisible;
		$activated = $allVisible;
		$byUserGroup = null;


		$createdAt = null;
		$postYear = "post_year";

		$where = array();

		if(!$request->isMethod('POST') && $session->has('user_manager/last_activity') && (time() - $session->get('user_manager/last_activity') > (15*60)))
		{
			$session->remove('user_manager/limit');
			$session->remove('user_manager/page');
			$session->remove('user_manager/where');
			$session->remove('user_manager/ordering');
			$session->remove('user_manager/visibility');
			$session->remove('user_manager/activated');
			$session->remove('user_manager/group');

			$session->remove('user_manager/created_at');
		}
		$session->set('user_manager/last_activity', time());

		if(!$request->isMethod('POST') && ($session->has('user_manager/limit') 
			&& $session->has('user_manager/page') 
			&& $session->has('user_manager/where') 
			&& $session->has('user_manager/ordering') 
			&& $session->has('user_manager/visibility')
			&& $session->has('user_manager/activated')
			&& $session->has('user_manager/group')
			&& $session->has('user_manager/created_at'))
		  )
		{
				$limit = $session->get('user_manager/limit');
				$page = $session->get('user_manager/page');
				$where = $session->get('user_manager/where');
				$visibility = $session->get('user_manager/visibility');
				$activated = $session->get('user_manager/activated');
				$ordering = $session->get('user_manager/ordering');
				$byUserGroup = $session->get('user_manager/group');

				$createdAt = $session->get('user_manager/created_at');
		}
		else{

			if($request->request->has('group')){
				$req_byUserGroup = $request->request->get('group');
				if(!empty($req_byUserGroup)){
					$byUserGroup = $req_byUserGroup;
					$where['g.id'] = $byUserGroup;
				}
			}

			if($request->request->has('visibility')){
				$visibility = $request->request->get('visibility');
			}

			if($request->request->has('activated')){
				$activated = $request->request->get('activated');
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

			if($request->request->has('created_at')){
				$createdAt = $request->request->get('created_at');
			}

			if($visibility != $allVisible){
				$where['u.locked'] = (bool) $visibility;
			}

			if($activated != $allVisible){
				$where['u.activated'] = (bool) $activated;
			}

			if($createdAt == $allVisible){
				$createdAt = null;
			}

		}

		list($sort, $order) = explode(" ",$ordering);

		/*$totalItems = $this->getDoctrine()->getManager()
			->getRepository('UserModule:User')
			->countUsers($where);*/

		
		$createdAtClosure = function($qb) use ($createdAt, $postYear){
			if(!empty($createdAt)){
				if($createdAt == $postYear){
					$dt = new \Datetime('12 months ago');
					$qb->andWhere('u.createdAt <= :createdAt')
						->setParameter('createdAt', $dt->format('Y-m-d H:i:s'));
				}
				else{
					$dt = new \Datetime($createdAt);
					$qb->andWhere('u.createdAt >= :createdAt')
						->setParameter('createdAt', $dt->format('Y-m-d H:i:s'));
				}
			}
			return $qb;
		};

		$qb = $this->getDoctrine()->getManager()
			->getRepository('UserModule:User')
			->getCountUsersQB($where);

		$qb = $createdAtClosure($qb);

		$totalItems = $qb->getQuery()->getSingleScalarResult();

		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;

		if($request->isMethod('POST'))
		{
			$session->set('user_manager/limit',$limit);
			$session->set('user_manager/page',$page);
			$session->set('user_manager/where',$where);
			$session->set('user_manager/ordering',$ordering);
			$session->set('user_manager/visibility',$visibility);
			$session->set('user_manager/activated',$activated);
			$session->set('user_manager/group',$byUserGroup);

			$session->set('user_manager/created_at',$createdAt);
			return $this->redirect($this->generateUrl("rgs_admin_gestion_users"));
		}

		/*$users = $this->getDoctrine()->getManager()
			->getRepository('UserModule:User')
			->findUsers($limit, $page, $where, array($sort => $order));*/

		$qb = $this->getDoctrine()->getManager()
			->getRepository('UserModule:User')
			->getFindUsersQB($limit, $page, $where, array($sort => $order));

		$qb = $createdAtClosure($qb);

		$users = new Paginator($qb);

		$entityExt = new \Novice\Form\Extension\Entity\EntityExtension($this->getDoctrine(), array(
		'class' => 'UserModule:Group',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('g')
					->orderBy('g.name', 'ASC');
		},
        'name' => 'group',
		'feedback' => false,
		'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'All Groups',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 2,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$visibilityField = new SelectField(array(
			'name' => 'visibility',
			'empty_option' => false,
			'options' => array(
				$allVisible => "- Etat",
				User::NOT_LOCKED => $this->get('translator')->trans("user.not_locked",array(),"UserModule"),
				User::LOCKED => $this->get('translator')->trans("user.locked",array(),"UserModule"),
			),
			'feedback' => false,
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'All',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
		
		$activatedField = new SelectField(array(
			'name' => 'activated',
			'empty_option' => false,
			'options' => array(
				$allVisible => "- Activé",
				User::ACTIVATED => $this->get('translator')->trans("user.activated",array(),"UserModule"),
				User::NOT_ACTIVATED => $this->get('translator')->trans("user.not_activated",array(),"UserModule"),
			),
			'feedback' => false,
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'All',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$orderingField = new SelectField(array(
			'name' => 'ordering',
			'empty_option' => false,
			'options' => array( 
				"u.login ASC" => "Login ascending",
				"u.login DESC" => "Login descending",
				"u.email ASC" => "Email ascending",
				"u.email DESC" => "Email descending",
				"u.lastLogin ASC" => "Last visit date ascending",
				"u.lastLogin DESC" => "Last visit date descending",
				"u.createdAt ASC" => "Registration date ascending",
				"u.createdAt DESC" => "Registration date descending",
				"u.id ASC" => "Id ascending",
				"u.id DESC" => "Id descending",
			),
			'feedback' => false,
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'Order by',
			//'data-theme' => 'classic',
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
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => 'Number per page',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$createdAtField = new SelectField(array(
			'name' => 'created_at',
			'empty_option' => false,
			//'empty_option_text' => '- '.$this->get('translator')->trans("user.created_at",array(),"UserModule"),
			//'bootstrap' => false,
			'options' => array( 
				$allVisible => '- '.$this->get('translator')->trans("user.created_at",array(),"UserModule"),
				'today' => 'Aujourd\'hui',
				'1 week ago' => 'La dernière semaine', //Monday this week
				'first day of' => 'Ce mois-ci',
				'3 months ago' => 'Les trois derniers mois',
				'6 months ago' => 'Les six derniers mois',
				'12 months ago' => 'La dernière année',
				$postYear => 'Plus d\'une année',),
			'feedback' => false,
			'attributes' => array(
			/*'class' => 'selectmenu selectmenu-submit',
			'onchange' => 'this.form.submit();',*/
			'style' => 'width: 99%',
			'data-placeholder' => '- '.$this->get('translator')->trans("user.created_at",array(),"UserModule"),
			//'data-theme' => 'classic',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$this->assign("users", $users);

		$this->assign("pagesCount", $pagesCount);

		$this->assign("page", $page);

		$this->assign("limitWidget", $limitField->setValue($limit)->buildWidget());

		$this->assign("orderingWidget", $orderingField->setValue($ordering)->buildWidget());

		$this->assign("activatedWidget", $activatedField->setValue($activated)->buildWidget());
		
		$this->assign("visibilityWidget", $visibilityField->setValue($visibility)->buildWidget());

		$this->assign("groupWidget", $entityExt->createField()->setValue($byUserGroup)->buildWidget());

		$this->assign("createdAtWidget", $createdAtField->setValue($createdAt)->buildWidget());
	}

	private function processPostGestion(Request $request, $itemType)
	{
		if($request->isMethod('POST'))
		{
			switch($itemType)
			{
				case 'user':
					$addRouteId = 'rgs_admin_users_add';
					$editRouteId = 'rgs_admin_users_edit';
					$repositoryName = 'UserModule:User';
					break;
				default:
					throw new \InvalidArgumentException('The second argument in '.__METHOD__.' must be string: \'user\'');
					return;
			}

			//dump($request->request->all());
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);
				
				if($submit == "add.new"){
					return $this->redirect($this->generateUrl($addRouteId));
				}

				//exit($submit);

				if($request->request->has('uid')){
					$ids = $request->request->get('uid');
					$firstId = $ids[0];
					if($this->container->get('session')->isAuthenticated()){
						$key = array_search ( $this->get('app.user')->getData()->getId() , $ids, false);
						if($key !== false){
							unset($ids[$key]);
						}
					}
					if($submit == "lock"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->lock($ids);
					}
					else if($submit == "unlock"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->lock($ids, User::NOT_LOCKED);
					}
					else if($submit == "activate"){
						$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
						->activate($ids, User::ACTIVATED);
					}
					else if($submit == "delete"){
							$result = $this->getDoctrine()->getManager()->getRepository($repositoryName)
							->deleteByIds($ids);
					}
					else if($submit == "edit"){
						return $this->redirect($this->generateUrl($editRouteId, array('id'=>$firstId)));
					}
				}
			}
				//dump($request->request->all());
				//dump($request->request->get('uid'));
			//exit();
		}
	}
	
	private function confirmPasswordUpdate(\Novice\Form\Form $form, $fieldName1, $fieldName2)
	{
		if( !($retour = StringUtils::equals($form->getField($fieldName1)->value(), $form->getField($fieldName2)->value())) ){
			$form->getField($fieldName1)->setWarningMessage("Password don't match");
			$form->getField($fieldName2)->setWarningMessage("Password don't match");
		}

		return $retour;
	}
	
	public function executeEditUser(Request $request)
	{	
		$this->setView('file:[RgsAdminModule]Users/editUser.php');

		if($request->attributes->has('id')){
			$user = $this->getDoctrine()->getManager()->getRepository('UserModule:User')
							->findOneById($request->attributes->get('id'));
		}
		else{
			$user = new User();
		}

		try{
			$form = $this->buildForm(new \Rgs\UserModule\Form\UserFormBuilder($user))
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
				$canSave = $this->confirmPasswordUpdate($form, 'password_update', 'confirm');
				$saveNewPass = false;
				$pass = $form->getField('password_update')->value();
				
				if($user->getId() == null){
					if(empty($pass)){
						$canSave = false;
						$form->getField('password_update')->setWarningMessage("Le nouveau user doit avoir un password");
					}
					$saveNewPass = true;
				}
				else if(!empty($pass) && $user->getId() != null){
					$saveNewPass = true;
				}
				
				if($canSave)
				{
					if($saveNewPass){
						$user->setPassword(Password::hash($form->getField('password_update')->value()));
					}
					$em->persist($user);
					$em->flush();
					$em->getConnection()->commit();

					return $this->redirect($this->generateUrl('rgs_admin_gestion_users'));
				}
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_admin_users_edit', array(
					"id" => $user->getId(),
				), 
				true).'" class="alert-link">fill in the form</a> and try submitting again.');
			}
			else if($e instanceof \Doctrine\DBAL\Exception\UniqueConstraintViolationException){
				if(count($em->getRepository('UserModule:User')->findByLogin($user->getLogin())) > 0){
					$form->getField('login')->setWarningMessage(': "'.$user->getLogin().'" est déjà enregistré, veuillez changer de login !');
				}
				if(count($em->getRepository('UserModule:User')->findByEmail($user->getEmail())) > 0){
					$form->getField('email')->setWarningMessage(': Cette adresse mail est déjà enregistrée, veuillez changer !');
				}
			}
			else{
				throw $e;
			}
		}

		$this->assign(array('title' => 'Edit',
							'form' => $form->createView()));
	}
}
