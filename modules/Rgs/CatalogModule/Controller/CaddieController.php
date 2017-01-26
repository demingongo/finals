<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\Security\Core\Util\StringUtils;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Categorie,
	Rgs\CatalogModule\Entity\Etat;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group;
use Novice\Form\Extension\Entity\EntityExtension;
use Symfony\Component\Routing\Annotation\Route; //pour annotation

use Novice\Annotation as NOVICE; //pour annotations de Novice (Template, Service, Assign, AttributeConverter, ...)

use Novice\Templating\Assignor\ErrorMessages;
use Rgs\CatalogModule\Validator\ArticleValidator;

use Rgs\CatalogModule\Form\ArticleForm;

class CaddieController extends \Novice\BackController
{
	
	/**
	 * @NOVICE\Service
	 */
	private $request_stack;
	
	/**
	 * @NOVICE\Service()
	 */
	private $session;
	
	/**
	 * @NOVICE\Service("rgs.caddie")
	 */
	private $caddie;
	
	
	/**
     * @Route("/caddie", name="rgs_catalog_caddie")
	 * @NOVICE\Template()
     */
	public function executeCaddie($request)
	{
		if($request->isMethod('POST')){
			if($request->request->has('caddie') && is_array($submit = $request->request->get('caddie'))){
				$submit = end($submit);
				if($submit == "confirm"){
					return $this->redirect($this->generateUrl("rgs_catalog_user_reservation_add"));
				}
				else if($submit == "remove"){
					$this->caddie->remove($request->request->get('id'));
				}
				else if($submit == "removeAll"){
					$this->caddie->removeAll();
				}
			}
			else if($request->request->has('quantite')){
				$this->caddie->setQuantity($request->request->get('id'), $request->request->get('quantite'));
			}
			
			return $this->redirect($this->generateUrl("rgs_catalog_caddie"));
		}
		
		//return array('titre' => 'Panier');
	}
	
	/**
     * @Route("/caddie/add", name="rgs_catalog_caddie_add")
	 *
	 * @NOVICE\AttributeConverter("articleForm", 
	 *						class="Rgs\CatalogModule\Form\ArticleForm",
	 * 						from=NOVICE\AttributeConverter::REQUEST)
     */
	public function executeCaddieAdd($request, ArticleForm $articleForm)
	{		
		$response = $this->redirect($this->generateUrl('rgs_catalog_caddie'));
		
		if($articleForm->getId() != null){
			$article = $this->getDoctrine()->getManager()->getRepository('RgsCatalogModule:Article')->findOneById($articleForm->getId());
			if($article != null){
				if(!$this->caddie->add($article)){
					if( !$this->caddie->has($article)
						&& $request->headers->has('referer') 
						&& !StringUtils::equals($request->headers->get('referer'), $request->getUri())){
						$response = $this->redirect($request->headers->get('referer'));
						$this->session->getFlashBag()->set('error', 'Could not add the article to the caddie');
					}
					else if( $this->caddie->has($article)){
						$this->session->getFlashBag()
						->set('error', 'Il ne reste que '.$article->getStock().' exemplaire(s) de ce produit en stock.');
					}
				}
			}
		}
		
		return $response;
	}
	
}
