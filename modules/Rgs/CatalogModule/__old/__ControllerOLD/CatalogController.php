<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Rgs\CatalogModule\Entity\Article;
use Rgs\CatalogModule\Entity\Categorie;
use Rgs\UserModule\Entity\User;
use Rgs\UserModule\Entity\Group;


class CatalogController extends \Novice\BackController
{
	
	const NUM_ITEMS = 11;
	
	//public function executeIndex( $request)
	//{
		//$cat = new Categorie("Consoles");
		
		//$nsm = $this->get('nested_set')->getManager('RgsCatalogModule:Categorie');

		//$rootNode = $nsm->createRoot($cat);
		
		/*$em = $this->get('managers')->getManager();
		
		$cat = $em
				->getRepository('RgsCatalogModule:Categorie')
				->findOneByName('Consoles');
		
		$article = new Article("Playstation 1");
		$article->setCategorie($cat);
		
		try{

		$em->persist($article);
		$em->flush();
		}
		catch(\Exception $e){
			dump($e->getMessage());
		}*/

		/*dump($this->get('templating'));
		dump($this->get('app'));
		exit(__METHOD__);*/

		//$em = $this->getDoctrine()->getManager();

		//$this->createGroup();
		
		/*
			$group = $em->getRepository('UserModule:Group')->findOneById(2);
			$this->createUser($group);
		*/

		//$this->updateUser();

		/*$user = $em->getRepository('UserModule:User')->findOneById(2);
		$roles = $user->getRoles();
		dump($roles);
		dump($user);*/

		//dump($this->get('translator')->getLocale());
		//dump($this->get('translator')->trans('security.login.remember_me', array(), 'RgsCatalogModule', 'fr'));
		//exit(__METHOD__);

	/*	$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días',
							'controller' => $this));
	}*/

	private function createGroup(){
		$em = $this->get('managers')->getManager();

		$group = new Group();

		$group
			->setName('Gestionnaire')
			->setRoles(array(User::ROLE_SUPER_ADMIN));

		try{
			$em->persist($group);
			$em->flush();
		}
		catch(\Exception $e){
			echo $e->getMessage()."<br>";
		}

		dump($group);
	}

	private function createUser(Group $group){
		$em = $this->get('managers')->getManager();

		$user = new User();

		$user
			->setLogin('Bang')
			->setEmail('in')
			->setPassword('yourface')
			->setGroup($group);
			//->addRole(User::ROLE_SUPER_ADMIN);

		try{
			$em->persist($user);
			$em->flush();
		}
		catch(\Exception $e){
			echo $e->getMessage()."<br>";
		}

		dump($user);
	}

	private function updateUser(){
		$em = $this->get('managers')->getManager();

		$group1 = $em->getRepository('UserModule:Group')->findOneById(1);
		$group2 = $em->getRepository('UserModule:Group')->findOneById(2);

		$user1 = $em->getRepository('UserModule:User')->findOneById(1);
		$user2 = $em->getRepository('UserModule:User')->findOneById(2);

		$group1->setName('Client');
		$group2->setName('Gestionnaire');

		$user1->setGroup($group2);
		$user2->setGroup($group1);

		try{
			$em->persist($user1);
			$em->persist($user2);
			$em->flush();
		}
		catch(\Exception $e){
			echo $e->getMessage()."<br>";
		}

		dump($user1->getRoles());
		dump($user2->getRoles());

		dump($user1);
		dump($user2);
	}
}
