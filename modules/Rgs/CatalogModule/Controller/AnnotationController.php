<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Category,
	Rgs\CatalogModule\Entity\Etat;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group;
use Novice\Form\Extension\Entity\EntityExtension;
use Symfony\Component\Routing\Annotation\Route; //pour annotation

use Novice\Annotation as NOVICE; //pour annotations de Novice (Template, Service, Assign, AttributeConverter, ...)

use Novice\Templating\Assignor\ErrorMessages;
use Rgs\CatalogModule\Validator\ArticleValidator;

use Firebase\JWT\JWT;

use Novice\Password;


class AnnotationController extends \Novice\BackController
{
	
	const NUM_ITEMS = 11;
	
	/**
	 * @NOVICE\Service
	 */
	private $request_stack;
	
	
	/**
	 * @NOVICE\Assign("tinymce_base_url", route_names={"rgs_catalog_index", "rgs_catalog_articles_all"})
	 */
	public function getBaseUrl()
	{
		return $this->request_stack->getCurrentRequest()->getBaseUrl();
	}


	private function createToken(){
		$tokenId    = base64_encode(mcrypt_create_iv(32));

		dump($tokenId);
		

		$issuedAt   = time();
	    $notBefore  = $issuedAt + 10;             //Adding 10 seconds
	    $expire     = $notBefore + 60;            // Adding 60 seconds
	    $serverName = $this->get('request_stack')->getCurrentRequest()->server->get('SERVER_NAME'); // Retrieve the server name
    
	    /*
	     * Create the token as an array
	     */
	    $data = [
	        'iat'  => $issuedAt,         // Issued at: time when the token was generated
	        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
	        'iss'  => $serverName,       // Issuer
	        //'nbf'  => $notBefore,        // Not before
	        //'exp'  => $expire,           // Expire
	        'data' => [                  // Data related to the signer user
	            'userId'   => 1, // userid from the users table
	            'userName' => "sdemingongo@email.address", // User name
	        ]
	    ];

		dump($data);

		$jwtKey = 'secret';
			
		try{
			$jwtKey = $this->container->getParameterBag()->get('jwt_key');
		}
		catch(\Exception $e){
		}

		dump($jwtKey);

		$secretKey = Password::hash(base64_encode(md5($jwtKey)), array("cost" => PASSWORD_BCRYPT_DEFAULT_COST, "salt" => md5($jwtKey) ));	
		dump($secretKey);

		$jwt = JWT::encode(
		    $data,      //Data to be encoded in the JWT
	        $secretKey, // The signing key
	        'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
        
		$unencodedArray = ['jwt' => $jwt];
	    dump(json_encode($unencodedArray));

		$s2 = Password::hash(base64_encode(md5($jwtKey)), array("cost" => PASSWORD_BCRYPT_DEFAULT_COST, "salt" => md5($jwtKey)));
		dump($s2);
		$decoded = (array) JWT::decode($jwt, $s2, array('HS512'));

		dump($decoded["data"]);

		exit(__METHOD__);
	}
	
	
	/**
     * @Route("/{homepage}", name="rgs_catalog_index" , defaults={"homepage": "home"}, requirements={"homepage": "home|accueil|index"})
	 * @NOVICE\Template("file:[RgsCatalogModule]index.tpl")
     */
	public function executeIndex($request)
	{

		$em = $this->getDoctrine()->getManager();

		$ads = $em->getRepository('RgsCatalogModule:Advertisement')->findBy(['published' => true], [ 'updatedAt' => 'DESC']);
		
		/*findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)

		$persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);

        return $persister->loadAll($criteria, $orderBy, $limit, $offset);*/

		return array('controller' => $this, 'ads' => $ads);
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
     * @Route("/articles/all/{_page}", 
	 *			name="rgs_catalog_articles_all", 
	 * 			defaults={"_page": 1, "article_name": "attr_conv"}, 
	 * 			requirements={"_page": "\d+"})
	 *
	 *
	 * @NOVICE\AttributeConverter("article_attr", 
	 *						class="Rgs\CatalogModule\Entity\Article",
	 * 						from=NOVICE\AttributeConverter::REQUEST, 
	 *						editor="article_name_editor")
	 *
	 * @NOVICE\Template
     */
	public function executeArticlesAll($_page, Article $article_attr)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		
		//$this->assign("categories", $this->getDoctrine()->getManager()->getRepository("RgsCatalogModule:Category")->findAll());
		
		$formError = new ErrorMessages();
		
		//if($request->isMethod('POST')){
			$validator = new ArticleValidator();
			$validator->validateRequest($request, $article_attr, $formError);
			//$validator->validate($article_attr, $formError);
			
			// s'il n'y a pas de messages d'erreurs
			if(!$formError->hasError()){
				// traitement + redirect
			}
		//}
		$this->assign($formError);
		
		$where = array();
		
		$filtre = false;
		
		if($request->query->has('category')){
			$req_byCategory = $request->query->get('category');
			if(!empty($req_byCategory)){
				$where['a.category'] = $req_byCategory;
				$filtre = true;
			}
		}
		if($request->query->has('etat')){
			$req_byEtat = $request->query->get('etat');
			if(!empty($req_byEtat)){
				$where['a.etat'] = $req_byEtat;
				$filtre = true;
			}
		}
		
		$limit = 6;
		
		$page = $_page;//$request->attributes->get('_page');
		
		$em = $this->getDoctrine()->getManager();
		$paginator = $em->getRepository('RgsCatalogModule:Article')->getFrontArticles($limit, $page, $where);

		$articles = $paginator->getQuery()->getResult();

		/*dump($articles);
		foreach($articles as $a){
			dump($a->getName());
			dump($a->getCategory()->getName());
			dump($a->getEtat());
		}
		exit(__METHOD__);*/

		$this->assign("paginator", $paginator);
		
		$this->assign("articles", $articles);
		
		$this->assign("titre", "Tous les articles");
		
		$this->assign("nofilterHref", $this->generateUrl($request->attributes->get('_route')));
		
		$this->assign("filter", $filtre);
		
		//$this->setView("file:[RgsCatalogModule]articlesAll.tpl");
	}

	/**
	 * @NOVICE\Assign("categoryWidget", route_names={"rgs_catalog_articles_all"})
	 */
	public function getCategoryWidget(Request $request)
	{
		$byCategory = null;
		
		if($request->query->has('category') && !empty($request->query->get('category'))){
			$byCategory = $request->query->get('category');
		}

		$entityExtCat = new EntityExtension($this->getDoctrine(), array(
		'label' => 'Category',
		'class' => 'RgsCatalogModule:Category',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($repository) {
				/*$rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($em);
				$sql = $repository->getSQLFrontCategories($rsm);*/
				return $repository->getFrontCategoriesQB();
				//return $em->createNativeQuery($sql, $rsm);
		},
        'name' => 'category',
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

		return $entityExtCat->createField()->setValue($byCategory)->buildWidget();
	}


	/**
	 * @NOVICE\Assign("etatWidget", route_names={"rgs_catalog_articles_all"})
	 */
	public function getEtatWidget(Request $request)
	{
		$byEtat = null;
		
		if($request->query->has('etat') && !empty($request->query->get('etat'))){
			$byEtat = $request->query->get('etat');
		}

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
			'data-minimum-results-for-search' => 3,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		return $entityExtEtat->createField()->setValue($byEtat)->buildWidget();
	}
}
