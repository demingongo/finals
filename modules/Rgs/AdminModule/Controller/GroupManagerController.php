<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Util\StringUtils;
use Rgs\UserModule\Entity\Group;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

use Novice\Form\Field\SelectField;
use Novice\Password;

use Symfony\Component\Debug as Symfony_Debug;

use Doctrine\ORM\Tools\Pagination\Paginator;

class GroupManagerController extends \Novice\BackController
{
	public function executeGestionGroup(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Users/gestionGroup.php');

		$session = $this->get('session');

		$r = $this->processPostGestion($request);
		if(is_object($r) && $r instanceof Response)
			return $r;

		$where = array();

		if($request->isMethod('POST'))
		{
			return $this->redirect($this->generateUrl("rgs_admin_gestion_groups"));
		}

		$groups = $this->getDoctrine()->getManager()
			->getRepository('UserModule:Group')
			->findAll();

		$this->assign("groups", $groups);
	}

	private function processPostGestion(Request $request)
	{
		if($request->isMethod('POST'))
		{
			
			$repositoryName = 'UserModule:Group';

			//dump($request->request->all());
			if($request->request->has('submit') && is_array($submit = $request->request->get('submit'))){
				$submit = end($submit);

				if($request->request->has('gid')){
					$ids = $request->request->get('gid');
					$firstId = $ids[0];
					if($this->container->get('session')->isAuthenticated()){
						$key = array_search ( $this->get('app.user')->getData()->getGroup()->getId() , $ids, false);
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
						->lock($ids, Group::NOT_LOCKED);
					}
				}
			}
				//dump($request->request->all());
				//dump($request->request->get('uid'));
			//exit();
		}
	}
}
