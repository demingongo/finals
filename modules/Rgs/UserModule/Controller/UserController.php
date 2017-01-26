<?php
namespace Rgs\UserModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie,
	Symfony\Component\Security\Core\Util\SecureRandom,
	Symfony\Component\Security\Core\Util\StringUtils;
use Novice\User\UserSessionManager,
	Novice\Form\Extension\Csrf\CsrfExtension,
	Novice\Form\Extension\Securimage\SecurimageExtension,
	Novice\Password,
	Novice\StreamedErrorResponse;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group,
	Rgs\UserModule\Entity\AuthToken,
	Rgs\UserModule\Form\LoginFormBuilder,
	Rgs\UserModule\Form\RegisterFormBuilder;

class UserController extends \Novice\BackController
{
	public function executeLogin(Request $request)
	{	
		$this->setView("file:[RgsCatalogModule]login.php");

		$session = $this->get('session');

		if($session->isAuthenticated()){
			return $this->redirect($this->generateUrl('rgs_catalog_index'));
		}

		$user = new User();

		$csrfExtension = new CsrfExtension($session);

		$form = $this->buildForm(new LoginFormBuilder($user))
					 ->addExtension($csrfExtension)
					 ->form();

		$form->handleRequest($request);

		try{
			if ($form->isValid())
			{	
				$result = $this->getDoctrine()->getManager()->getRepository('UserModule:User')->findOneByLogin($user->getLogin());
				if(null == $result || !Password::verify($user->getPassword(), $result->getPassword())){
					$form->getField('login')->setWarningMessage();
					$form->getField('password')->setWarningMessage();
					$session->getFlashBag()->set('notice', 'Login ou Password incorrect.');
				}
				else if(!$result->isActivated()){
					$session->getFlashBag()->set('notice', 'Veuillez confirmez l\'enrgistrement de votre compte par email.');
				}
				else{
					$usm = new UserSessionManager($session);
					$usm->login($result->getId());
				
					$this->get('app.user')->setData($result);
					$response = $this->redirect($this->generateUrl('rgs_catalog_index'));
					if(null != $form->getField('remember_me') && $form->getField('remember_me')->value()){
						$em = $this->getDoctrine()->getManager();
						$auth = new AuthToken();
						$auth->setUser($result);
						$date = new \DateTime('now');
						$date->add(new \DateInterval('P2D'));
						$auth->setExpiresAt($date);
						$generator = new SecureRandom();
						$token = bin2hex($generator->nextBytes(32));
						$auth->setToken(Password::hash($token));
						$em->persist($auth);
						$em->flush();
						$usm->createLoginCookie($response, $auth->getId(), $token, $date);
					}
					return $response;
				}
			}
		}
		catch(\Novice\Form\Extension\Csrf\CsrfSecurityException $e){ //\Novice\Form\Exception\SecurityException
			$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_catalog_login', array(), true).
				'" class="alert-link">fill in the form</a> and try submitting again.');
		}

		$this->assign(array('title' => 'Login',
							'form' => $form->createView()));
	}

	public function executeLogout(Request $request)
	{
		$reponse = $this->redirect($this->generateUrl('rgs_catalog_index'));

		$usm = new UserSessionManager($this->container->get('session'));
		
		if($usm->retrieveLoginCookie($request)->hasCookie()){
			$idToken = $usm->getCookieId();
			try{
				$em = $this->getDoctrine()->getManager();
				$dbToken = $em->getRepository('UserModule:AuthToken')->findOneById($idToken);
				$em->remove($dbToken);
				$em->flush();
			}
			catch(\Exception $e){}
			$usm->clearLoginCookie($reponse);
		}
		$usm->logout(); //$this->get('session')->invalidate();
		return $reponse;
	}

	public function executeRegister(Request $request)
	{
		//return StreamedErrorResponse::createResponse($this->get('templating'),'404');

		$this->setView("file:[RgsCatalogModule]register.php");

		$session = $this->get('session');

		if($session->isAuthenticated()){
			return $this->redirect($this->generateUrl('rgs_catalog_index'));
		}

		$user = new User();

		$generator = new SecureRandom();
		$csrfExtension = new CsrfExtension($session, $generator->nextBytes(32), 15*60);

		$formBuilder = new RegisterFormBuilder($user);
		$formBuilder->setContainer($this->container);
		$formBuilder->form()->setName('register');
		$formBuilder->addExtension($csrfExtension)->addExtension(new SecurimageExtension())->build();  
		$form = $formBuilder->form();

		$form->handleRequest($request);

		try{	

			if($form->isValid() && $user->isValid()) {
				if($this->registerConfirmPassword($form, 'password', 'confirm'))
				{
					try{
						$user->setPassword(Password::hash($user->getPassword()));
						$confirmationToken = bin2hex($generator->nextBytes(32));
						$user->setConfirmationToken(Password::hash($confirmationToken));
						$em = $this->getDoctrine()->getManager();
						$group = $em->getRepository('UserModule:Group')->findOneByName('Client');
						$user->setGroup($group);
						$em->persist($user);
						$em->flush();

						$this->get('rgs.mailer')->sendRegisterConfirm($user, 
							$this->generateUrl('rgs_catalog_confirm_registration',array(
							'token' => $confirmationToken,
							'login' => $user->getLogin(),
						), true));

						$session->getFlashBag()->set('success','User validé, veuillez confirmez l\'enrgistrement votre compte par email');
						return $this->redirect($this->generateUrl('rgs_catalog_index',array()));
					}
					catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
						$form->getField('login')->setWarningMessage(': "'.$user->getLogin().'" est indisponible, changez de login !');
					}
				}
			}
		}
		catch(\Novice\Form\Exception\SecurityException $e){
			$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('rgs_catalog_register', array(), true).'" class="alert-link">fill in the form</a> and try submitting again.');
		}

		$this->assign('form', $form->createView());
	}

	public function executeConfirmRegistration(Request $request)
	{
		$this->setView("file:[RgsCatalogModule]confirmRegistration.php");

		$session = $this->get('session');

		$login = $request->attributes->get('login');
		$token = $request->attributes->get('token');

		$message = 'Données de confirmation non valides.';
		
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserModule:User')->findOneByLogin($login);
		if(null != $user){
			if(!$user->isActivated() && Password::verify($token, $user->getConfirmationToken())){
				$user->setActivated(User::ACTIVATED);
				$em->persist($user);
				$em->flush();
				if(!$session->isAuthenticated()){
					$usm = new UserSessionManager($session);
					$usm->login($user->getId());
					$this->get('app.user')->setData($user);
				}
				$message = 'Enregistrement confirmé.';
			}
			else if($user->isActivated() && Password::verify($token, $user->getConfirmationToken())){
				$message = 'L\'enregistrement a déjà été confirmé.';
			}
		}
		
		$session->getFlashBag()->set('notice', $message);
		$this->assign('message', $message);

		//return $this->createHttpResponse();
	}

	private function registerConfirmPassword(\Novice\Form\Form $form, $fieldName1, $fieldName2)
	{
		if( !($retour = StringUtils::equals($form->getField($fieldName1)->value(), $form->getField($fieldName2)->value())) ){
			$form->getField($fieldName1)->setWarningMessage();
			$form->getField($fieldName2)->setWarningMessage();
		}

		return $retour;
	}
}
