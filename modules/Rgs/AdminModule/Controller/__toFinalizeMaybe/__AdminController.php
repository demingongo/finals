<?php
namespace Rgs\AdminModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rgs\CatalogModule\Entity\Categorie;
use Rgs\CatalogModule\Entity\Article;

use DoctrineModule\Form\Extension\EntityNode\EntityNodeExtension;

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
		/*$nsm = $this->get('nested_set')->getManager('RgsCatalogModule:Categorie');
		$cat_test = new Categorie();
		$node = $nsm->wrapNode($cat_test);
		if($request->isMethod('POST'))
			dump($request);

		$tree = $nsm->fetchTreeAsArray(8);

		foreach ($tree as $no) {
			echo str_repeat('&nbsp;&nbsp;', $no->getLevel()) . $no . "<br>";
		}
		dump($node);
		exit(__METHOD__);*/

		$nsm = $this->get('nested_set')->getManager('RgsCatalogModule:Categorie');

		$rootField = $nsm->getConfiguration()->getRootFieldName();
		$lftField = $nsm->getConfiguration()->getLeftFieldName();

		$em = $this->getDoctrine()->getManager();

		//dump($em->getClassMetadata('RgsCatalogModule:Categorie'));
		//dump($em->getClassMetadata('RgsCatalogModule:Article'));
		//exit(__METHOD__);

		/*$qb->select('c')
			->from('RgsCatalogModule:Categorie', 'c');

		$qb_p = $em->createQueryBuilder();
		$r_p =$qb_p->select('c.id , c.'.$lftField.' , c.'.$rootField )
			->from('RgsCatalogModule:Categorie', 'c')
			->where($qb_p->expr()->neq('c.published', ':published'))
			->setParameter('published', Categorie::PUBLISHED)
			->getQuery()->getArrayResult();
		dump($r_p);

		$ids = array();
		$lft_ids = array();
		$root_ids = array();
		foreach($r_p as $v){
				$ids[] = array_values($v)[0];
				$lft_ids[] = array_values($v)[1];
				$root_ids[] = array_values($v)[2];
		}
		if(!empty($ids)){
			dump($ids);
			dump($lft_ids);
			dump($root_ids);
			$qry = $em->createQueryBuilder();
			$qry->select('c.id')
			->from('RgsCatalogModule:Categorie', 'c');
			for($i = 0; $i<count($ids) ; $i++){
				if($i>0){
					$qry->orWhere($qry->expr()->andX($qb_p->expr()->eq('c.'.$rootField, $root_ids),));
				}
				else{
				}				
			}
		}
		exit;*/
		/*$id = 9;

		$qb = $em->createQueryBuilder();
		$qb ->select('c')
			->from('RgsCatalogModule:Categorie', 'c');
		if($id != null){
			$qb	->andWhere($qb->expr()->neq('c.'.$rootField, ':id'))
				->setParameter('id', $id);

			$qb2 = $em->createQueryBuilder();
			$r2 = $qb2->select('c')
				->from('RgsCatalogModule:Categorie', 'c')
				->where($qb2->expr()->eq('c.id', ':id'))
				->setParameter('id', $id)
				->getQuery()
				->execute();
			//dump($r2);
			if(!empty($r2[0])){
				$qb3 = $em->createQueryBuilder();
				$r3 = $qb3->select('c.id')
					->from('RgsCatalogModule:Categorie', 'c')
					->where($qb3->expr()->eq('c.'.$rootField, ':rootbound'))
					->andWhere($qb3->expr()->gte('c.'.$lftField, ':greaterbound'))
					->setParameter('rootbound', $r2[0]->getRootValue())
					->setParameter('greaterbound', $r2[0]->getLeftValue())
					->getQuery()
					->getArrayResult();
				//dump($r3);
				//$r3=array();
				if(!empty($r3)){				
					$qb->andWhere($qb->expr()->notIn('c.id', ':subQuery'))
						->setParameter('subQuery', $r3);
				}
			}
		}
		//dump($nsm->getConfiguration()->getBaseQueryBuilder()->getQuery());
		$nsm->getConfiguration()->setBaseQueryBuilder($qb);
dump($qb->getQuery());
//exit;
		$tree = $nsm->fetchTreeAsArray(8);

		dump($tree);

		foreach ($tree as $no) {
			echo str_repeat('-', $no->getLevel()) . $no . "<br>";
		}

		$nsm->getConfiguration()->resetBaseQueryBuilder();
		exit(__METHOD__);*/
		
		$this->setView('file:[RgsAdminModule]Content/editCategorie.php');

		if($request->attributes->has('id')){
			$categorie = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Categorie')
							->findOneById($request->attributes->get('id'));

			/*dump($categorie->getArticles());
			foreach($categorie->getArticles() as $a){
					$categorie->getArticles()->removeElement($a);
					//$a->setCategorie(null);
				}*/

			/*$em->getConnection()->beginTransaction();
			try {
				dump($categorie);
				foreach($categorie->getArticles() as $a){
					$categorie->getArticles()->remove($a->getId());
					//$a->setCategorie(null);
				}
				dump($categorie);
				exit();
				$em->persist($categorie);
				$em->flush();
				$em->getConnection()->commit();
			}
			catch (\Exception $e) {
				$em->close();
				$em->getConnection()->rollback();
				throw $e;
			}*/
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
				/*$nsm = $this->get('nested_set')->getManager('RgsCatalogModule:Categorie');

				$p_c = null;
				if($request->request->has($form->getName())){
					$r_f = $request->request->get($form->getName());
					if(!empty($r_f['parent_categorie']))
						$p_c = $r_f['parent_categorie'];
				}

				//dump($request->request);
				//dump($p_c);
				//exit(__METHOD__);


				if(empty($p_c)){
					$rootNode = $nsm->createRoot($categorie);
				}
				else{
					$parent = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Categorie')->findOneById($p_c);
					$parent_node = $nsm->wrapNode($parent);

					if($categorie->getId() == null){
						$parent_node->addChild($categorie);
					}
					else{
						$cat_node = $nsm->wrapNode($categorie);
						$cat_node->moveAsLastChildOf($parent_node);
					}
				}*/

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

		$categories = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Categorie')
			//->findBy(array(), array('name' => 'ASC'));
			->getCategories($limit, $page, $orderBy, $ascending);

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
	}

	public function executeGestionArticle(Request $request)
	{
		$this->setView('file:[RgsAdminModule]Content/gestionArticle.php');

		$r = $this->processPostGestion($request, 'article');
		if(is_object($r) && $r instanceof Response)
			return $r;

		$page = 1;
		$limit = 2;
		$ordering = "a.name ASC";
		
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

		$items = $this->getDoctrine()->getManager()
			->getRepository('RgsCatalogModule:Article')
			//->findBy(array(), array('name' => 'ASC'));
			->getArticles($limit, $page, $orderBy, $ascending);

		$this->assign("items", $items);

		$this->assign("limitOptions", array( 2, 5, 10, 15, 20, 25, 30, 50));
		$this->assign("limit", $limit);
		$this->assign("orderingOptions", 
			array( 
			"a.name ASC" => "Title ascending",
			"a.name DESC" => "Title descending",
			"a.id ASC" => "Id ascending",
			"a.id DESC" => "Id descending",
			)
		);
		$this->assign("ordering", $ordering);
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
