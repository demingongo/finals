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
	Rgs\UserModule\Form\LostPasswordFormBuilder,
	Rgs\UserModule\Form\ResetPasswordFormBuilder;

use Rgs\UserModule\Util\UserModuleUtils;

class SecurityController extends \Novice\BackController
{
	public function executeLogin(Request $request)
	{	
		if($request->attributes->has('view'))
			$view = $request->attributes->get('view');
		else
			$view = "file:[UserModule]Security/login.php";
		
		$this->setView($view);

		$session = $this->get('session');
		
		if($hasRedirect = $request->attributes->has('_redirect'))
			$response = $this->redirect($this->generateUrl($request->attributes->get('_redirect')));
		else
			$response = $this->redirect($request->getUriForPath('/'));

		if($session->isAuthenticated()){
			return $response;
		}
		
		if(!$hasRedirect){
			if($request->headers->has('referer') && !StringUtils::equals($request->headers->get('referer'), $request->getUri())){
				$response = $this->redirect($request->headers->get('referer'));
			}
		}
		
		$roles = [];
		if($request->attributes->has('_roles')){
			$roles = $request->attributes->get('_roles');
			if(!is_array($roles)){
				$roles = [$roles];
			}
		}

		$user = new User();

		$csrfExtension = new CsrfExtension($session, null, 25*60);

		$form = $this->buildForm(new LoginFormBuilder($user))
					 ->addExtension($csrfExtension)
					 ->form();

		$form->handleRequest($request);

		try{
			if ($form->isValid())
			{		
				$result = $this->getDoctrine()->getManager()->getRepository('UserModule:User')->findOneByLogin($user->getLogin());
				if(null == $result || !Password::verify($user->getPassword(), $result->getPassword())){
					$session->getFlashBag()->set('notice', $this->get('translator')->trans("Bad credentials", array(), "UserModule"));
				}
				else if(!$result->isActivated()){
					$session->getFlashBag()->set('notice', 'Veuillez confirmez l\'enrgistrement de votre compte par email.');
				}
				else if($result->isLocked()){
					$session->getFlashBag()->set('notice', sprintf('Le compte de l\'utilisateur "%s" est bloqué.', $result->getLogin()));
				}
				else{
					
					// check attribute _roles
					$hasPermission = true;
					foreach($roles as $role){
						if(!$result->hasRole($role)){
							$hasPermission = false;
							break;
						}
					}
					
					if(!$hasPermission){
						$session->getFlashBag()->set('notice', '403 Forbidden');
					}
					else{
						$usm = new UserSessionManager($session);
						$usm->login($result->getId());
				
						$this->get('app.user')->setData($result);
					
						$em = $this->getDoctrine()->getManager();
						$result->setLastLogin(new \DateTime('now'));
	
						if(null != $form->getField('remember_me') && $form->getField('remember_me')->value()){
							$generator = new SecureRandom();
							$utils = new UserModuleUtils();
						
							$auth = new AuthToken();
							$auth->setUser($result);
						
							//expires in 2 days
							$date = new \DateTime('now');
							$date->add(new \DateInterval('P2D'));
						
							$auth->setExpiresAt($date);
							$token = $utils->createRandomToken($generator);
							$auth->setToken(Password::hash($token));
							$em->persist($auth);
							$em->flush();
							$usm->createLoginCookie($response, $auth->getId(), $token, $date);
						}

						$em->persist($result);
						$em->flush();
					
						if( !$hasRedirect
						&& $this->get('session')->has('security_login_redirect')  
						&& !StringUtils::equals($this->get('session')->get('security_login_redirect'), $request->getUri())
						&& $request->query->has('redirect') 
						&& !StringUtils::equals($request->query->get('redirect'), $request->getUri())
						&& StringUtils::equals($request->query->get('redirect'), $this->get('session')->get('security_login_redirect'))){
							$response = $this->redirect($this->get('session')->get('security_login_redirect'));			
						}
						if($this->get('session')->has('security_login_redirect')){
							$this->get('session')->remove('security_login_redirect');
						}

						return $response;
					}
					
					
				}
			}
		}
		catch(\Novice\Form\Extension\Csrf\CsrfSecurityException $e){ 
			$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('user_security_login', array(), true).
				'" class="alert-link">fill in the form</a> and try submitting again.');
		}

		$this->assign(array('title' => 'Login',
							'form' => $form->createView()));
	}

	public function executeLogout(Request $request)
	{
		$response;

		if($request->attributes->has('_redirect'))
			$response = $this->redirect($this->generateUrl($request->attributes->get('_redirect')));
		else
			$response = $this->redirect($request->getUriForPath('/'));

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
			$usm->clearLoginCookie($response);
		}
		
		// to avoid changing language after logout, save locale in var $locale
		$locale = null;
		if ($request->hasPreviousSession()) {
			if ($request->getSession()->has('_locale')) {
				$locale = $request->getSession()->get('_locale');
			}
        }
		
		$usm->logout(); //$this->get('session')->invalidate();
		
		// reset local in session after logout
		if (!($locale === null)) {
            $request->getSession()->set('_locale', $locale);
        }
		
		return $response;
	}
	
	public function executeLostPassword(Request $request)
	{

		$this->setView("file:[UserModule]Security/lostPassword.php");

		$session = $this->get('session');

		if($session->isAuthenticated()){
			return $this->redirect($this->generateUrl('rgs_catalog_index'));
		}

		$user = new User();

		$generator = new SecureRandom();
		$csrfExtension = new CsrfExtension($session, $generator->nextBytes(32), 15*60);

		$formBuilder = new LostPasswordFormBuilder($user);
		$formBuilder->setContainer($this->container);
		$formBuilder->form()->setName('lost_password');
		$formBuilder->addExtension($csrfExtension)->addExtension(new SecurimageExtension())->build();  
		$form = $formBuilder->form();

		$form->handleRequest($request);

		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{	

			if($form->isValid()) {
				$result = $this->getDoctrine()->getManager()->getRepository('UserModule:User')->findOneByLogin($user->getLogin());
				if(null == $result){
					$session->getFlashBag()->set('notice', $this->get('translator')->trans("Bad credentials", array(), "UserModule"));
				}
				else{
					$utils = new UserModuleUtils();
					
					$confirmationToken = $utils->createRandomToken($generator);
					$result->setConfirmationToken(Password::hash($confirmationToken));
					$em->persist($result);
					$em->flush();
					
					$this->get('rgs.mailer')->sendLostPasswordConfirm($result, 
							$this->generateUrl('user_security_resetpassword',array(
							'token' => $confirmationToken,
							'slug' => $result->getSlug(),
					), true));

					$em->getConnection()->commit();

					$session->getFlashBag()
						->set('success','A mail has been send. Check your email inbox and follow the link to pursue the request.');
					return $this->redirect($this->generateUrl('rgs_catalog_index',array()));
				}
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('user_security_lostpassword', array(), true).'" class="alert-link">fill in the form</a> and try submitting again.');
			}
			else{
				throw $e;
			}
		}

		$this->assign('form', $form->createView());
	}
	
	
	public function executeResetPassword(Request $request)
	{

		$slug = $request->attributes->get('slug');
		$token = $request->attributes->get('token');

		$this->setView("file:[UserModule]Security/lostPassword.php");

		$session = $this->get('session');

		if($session->isAuthenticated()){
			return $this->redirect($this->generateUrl('rgs_catalog_index'));
		}

		$user = new User();

		$generator = new SecureRandom();
		$csrfExtension = new CsrfExtension($session, $generator->nextBytes(32), 15*60);

		$formBuilder = new ResetPasswordFormBuilder($user);
		$formBuilder->setContainer($this->container);
		$formBuilder->form()->setName('lost_password');
		$formBuilder->addExtension($csrfExtension)->build();  
		$form = $formBuilder->form();

		$form->handleRequest($request);

		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{	

			if($form->isValid()) {
				
				$utils = new UserModuleUtils();
				
				if($utils->compareFormFields($form, 'password', 'confirm')){
					$result = $this->getDoctrine()->getManager()->getRepository('UserModule:User')->findOneBySlug($slug);
					if( null == $result || !Password::verify($token, $result->getConfirmationToken()) ){
						$session->getFlashBag()->set('notice', $this->get('translator')->trans("Unknown", array(), "UserModule"));
					}
					else{
						// set new password and confirmationToken
						$result->setPassword(Password::hash($user->getPassword()));
						$result->setConfirmationToken("reset");
						$em->persist($result);
						$em->flush();
	
						$em->getConnection()->commit();
	
						$session->getFlashBag()
							->set('success','Thanks! Your new password has been set. You can now log in.');
						return $this->redirect($this->generateUrl('rgs_catalog_index',array()));
					}
				}
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('user_security_resetpassword', 
				array('slug' => $slug, 'token' => $token), true).'" class="alert-link">fill in the form</a> and try submitting again.');
			}
			else{
				throw $e;
			}
		}

		$this->assign('form', $form->createView());
	}
}
