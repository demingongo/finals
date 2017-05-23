<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\Security\Core\Util\StringUtils;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Categorie,
	Rgs\CatalogModule\Entity\Reservation,
	Rgs\CatalogModule\Entity\Request as UserRequest;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group;
use Novice\Form\Extension\Entity\EntityExtension;
use Symfony\Component\Routing\Annotation\Route; //pour annotation

use Novice\Annotation as NOVICE; //pour annotations de Novice (Template, Service, Assign, AttributeConverter, ...)
use Novice\Password;

use Novice\Templating\Assignor\ErrorMessages;
use Rgs\CatalogModule\Validator\UserRequestValidator;

/**
 * @Route("/user")
 */
class UserPortalController extends \Novice\BackController
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
	
	
	private function confirmPasswordUpdate(\Novice\Form\Form $form, $fieldName1, $fieldName2)
	{
		if( !($retour = StringUtils::equals($form->getField($fieldName1)->value(), $form->getField($fieldName2)->value())) ){
			$form->getField($fieldName1)->setWarningMessage("Password don't match");
			$form->getField($fieldName2)->setWarningMessage("Password don't match");
		}

		return $retour;
	}
	
	
	/**
     * @Route("/portal", name="rgs_catalog_user_profile")
	 * @NOVICE\Template("file:[RgsCatalogModule]User/portal.tpl")
     */
	public function executePortal($request)
	{
		$tab= $request->query->get('tab');
		
		$tab = $tab == "myreservations" || $tab == "myprofile" ? $tab : "myprofile";
		
		$user = $this->get('app.user')->getData();
		$form = $this->buildForm(new \Rgs\CatalogModule\Form\ProfileUpdateFormBuilder($user))
					 ->form();

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

					return $this->redirect($this->generateUrl("rgs_catalog_user_profile"));
				}
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$this->session->getFlashBag()->set('danger', '<b>Failure occured</b>');
			}
			else{
				throw $e;
			}
		}
		
		return array('titre' => 'User Portal', 'tab' => $tab, 'form' => $form->createView());
	}
	
	/**
     * @Route("/reservation/add", name="rgs_catalog_user_reservation_add")
     */
	public function executeReservationAdd($request)
	{	
		$r = new Reservation();
		$ras = $this->caddie->findAll();
		
		foreach($ras as $ra){
			$ra['article']['stock'] -= $ra['quantite'];
			$r->addReservationArticle($ra);
		}
		
		$date = new \DateTime('now');
		$date->add(new \DateInterval('P2D'));
		$r->setExpiresAt($date);
		$r->setUser($this->get('app.user')->getData());
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($r);
		$em->flush();
		
		$this->caddie->removeAll();
		return $this->redirect($this->generateUrl("rgs_catalog_user_profile", array("tab"=>"myreservations")));		
	}


	/**
     * @Route("/requestform", name="rgs_catalog_user_requestform")
	 * @NOVICE\Template("file:[RgsCatalogModule]User/requestform.php")
	 * @NOVICE\AttributeConverter("user_request", 
	 *						class="Rgs\CatalogModule\Entity\Request",
	 * 						from=NOVICE\AttributeConverter::FORMDATA)
     */
	public function executeRequestForm(UserRequest $user_request, Request $request)
	{	

		$formError = new ErrorMessages();	
		$validator = new UserRequestValidator();
			
		// s'il n'y a pas de messages d'erreurs
		if($validator->validateRequest($request, $user_request, $formError) && !$formError->hasError()){

			$file = $user_request->getImage();
			if($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile){
				$fileName = $this->get('rgs.file_uploader')->upload($file);
				$user_request->setImage($this->get('rgs.file_uploader')->getSubDir().'/'.$fileName);
			}

			// traitement + redirect
			$em = $this->getDoctrine()->getManager();
			$em->getConnection()->beginTransaction();
			try{
				$user_request->setUser($this->get('app.user')->getData());
				$em->persist($user_request);
				$em->flush();
				$em->getConnection()->commit();

				$this->session->getFlashBag()->set('success', '<b>Thank you !</b> You\'ll be notified by email of your request progression');

				return $this->redirect($this->generateUrl('rgs_catalog_index'));
			}catch(\Exception $e){
				$em->close();
				$em->getConnection()->rollback();
				$this->session->getFlashBag()->set('error', '<b>Failure occured</b>, <a href="'.$this->generateUrl($request->attributes->get('_route'))
					.'" class="alert-link">fill in the form</a> and try submitting again.('.$e->getMessage().')');
			}
			
		}
		$this->assign($formError);

		return array(
			"user_request" => $user_request
		);		
	}
	
}
