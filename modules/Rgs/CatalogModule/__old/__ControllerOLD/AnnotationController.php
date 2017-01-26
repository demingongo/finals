<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Categorie,
	Rgs\CatalogModule\Entity\Etat;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group;
use Novice\Form\Extension\Entity\EntityExtension;
use Symfony\Component\Routing\Annotation\Route; //pour annotation

use Novice\Annotation\Assign;
use Novice\Annotation\Template;

class AnnotationController extends \Novice\BackController
{
	
	const NUM_ITEMS = 11;
	
	
	/**
	 * @Assign("tinymce_base_url", route_names={"rgs_catalog_index", "rgs_catalog_articles_all"})
	 */
	public function getBaseUrl($request_stack)
	{
		return $request_stack->getCurrentRequest()->getBaseUrl();
	}
	
	
	/**
     * @Route("/{homepage}", name="rgs_catalog_index" , defaults={"homepage": "home"}, requirements={"homepage": "home|accueil|index"})
	 * @Template("file:[RgsCatalogModule]index.tpl")
     */
	public function executeIndex($request)
	{
		
		/*$this->get('templating')->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días',
							'controller' => $this));

		return "file:[RgsCatalogModule]articlesAll.tpl";*/

		return array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días',
							'controller' => $this);
	}
	
	/**
     * @Route("/language_{_locale}", name="rgs_catalog_language" , requirements={"_locale": "en|fr|es"})
     */
	public function executeLanguage($request)
	{	
		$referer = $request->headers->get('referer');
		
		return $this->redirect($referer);
	}
	
	/**
     * @Route("/articles/all/{_page}", name="rgs_catalog_articles_all", defaults={"_page": 1}, requirements={"_page": "\d+"})
     */
	public function executeArticlesAll($request)
	{	
		//$router = $this->get('router');
		//dump($router->match($request));
		//dump($request->attributes);
		
		/*dump($router->getRouteCollection()->get('rgs_catalog_articles_all'));
		
		exit(__METHOD__);*/
		
		$where = array();
		
		$entityExtCat = new EntityExtension($this->getDoctrine(), array(
		'label' => 'Categorie',
		'class' => 'RgsCatalogModule:Categorie',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->where('c.published = :p')
					->orderBy('c.name', 'ASC')
					->setParameter('p', Categorie::PUBLISHED);
		},
        'name' => 'categorie',
		'feedback' => true,
		'attributes' => array(
			//'class' => 'selectmenu selectmenu-submit',
			'style' => 'width: 90%',
			'data-placeholder' => 'All Categories',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			//'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
		
		$entityExtEtat = new EntityExtension($this->getDoctrine(), array(
		'label' => 'Etat',
		'class' => 'RgsCatalogModule:Etat',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('e')
					->where('e.published = :p')
					->orderBy('e.name', 'ASC')
					->setParameter('p', Etat::PUBLISHED);
		},
        'name' => 'etat',
		//'feedback' => false,
		'attributes' => array(
			//'class' => 'selectmenu selectmenu-submit',
			'style' => 'width: 90%',
			'data-placeholder' => 'All States',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			//'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
		
		$byCategorie = null;
		$byEtat = null;
		
		$filtre = false;
		
		if($request->query->has('categorie')){
			$req_byCategorie = $request->query->get('categorie');
			if(!empty($req_byCategorie)){
				$byCategorie = $req_byCategorie;
				$where['a.categorie'] = $byCategorie;
				$filtre = true;
			}
		}
		if($request->query->has('etat')){
			$req_byEtat = $request->query->get('etat');
			if(!empty($req_byEtat)){
				$byEtat = $req_byEtat;
				$where['a.etat'] = $byEtat;
				$filtre = true;
			}
		}
		
		$limit = 2;
		
		$page = $request->attributes->get('_page');	
		
		//dump($request->query->all());	exit(__METHOD__);
		
		$em = $this->get('managers')->getManager();
		$articles = $em->getRepository('RgsCatalogModule:Article')->getFrontArticles($limit, $page, $where);
		
		$this->assign("articles", $articles);
		
		//$this->assign("categorieWidget", $entityExtCat->createField()->setValue($byCategorie)->buildWidget());
		$this->assign("etatWidget", $entityExtEtat->createField()->setValue($byEtat)->buildWidget());
		
		$this->assign("titre", "Tous les articles");
		
		$this->assign("nofilterHref", $this->generateUrl($request->attributes->get('_route')));
		
		$this->assign("filter", $filtre);
		
		//$this->setView("file:[RgsCatalogModule]articlesAll.tpl");
	}

	/**
	 * @Assign("categorieWidget", route_names={"rgs_catalog_articles_all"})
	 */
	public function getCategorieWidget(Request $request)
	{
		$byCategorie = null;
		
		if($request->query->has('categorie')){
			$req_byCategorie = $request->query->get('categorie');
			if(!empty($req_byCategorie)){
				$byCategorie = $req_byCategorie;
			}
		}

		$entityExtCat = new EntityExtension($this->getDoctrine(), array(
		'label' => 'Categorie',
		'class' => 'RgsCatalogModule:Categorie',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->where('c.published = :p')
					->orderBy('c.name', 'ASC')
					->setParameter('p', Categorie::PUBLISHED);
		},
        'name' => 'categorie',
		'feedback' => true,
		'attributes' => array(
			//'class' => 'selectmenu selectmenu-submit',
			'style' => 'width: 90%',
			'data-placeholder' => 'All Categories',
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			//'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		return $entityExtCat->createField()->setValue($byCategorie)->buildWidget();
	}
}
