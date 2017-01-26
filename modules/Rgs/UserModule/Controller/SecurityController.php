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
					//$form->getField('login')->setWarningMessage();
					//$form->getField('password')->setWarningMessage();
					$session->getFlashBag()->set('notice', $this->get('translator')->trans("Bad credentials", array(), "UserModule"));
				}
				else if(!$result->isActivated()){
					$session->getFlashBag()->set('notice', 'Veuillez confirmez l\'enrgistrement de votre compte par email.');
				}
				else if($result->isLocked()){
					$session->getFlashBag()->set('notice', sprintf('Le compte de l\'utilisateur "%s" est bloqué.', $result->getLogin()));
				}
				else{
					$usm = new UserSessionManager($session);
					$usm->login($result->getId());
				
					$this->get('app.user')->setData($result);
					
					$em = $this->getDoctrine()->getManager();
					$result->setLastLogin(new \DateTime('now'));

					if(null != $form->getField('remember_me') && $form->getField('remember_me')->value()){
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
		catch(\Novice\Form\Extension\Csrf\CsrfSecurityException $e){ //\Novice\Form\Exception\SecurityException
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

	private function registerConfirmPassword(\Novice\Form\Form $form, $fieldName1, $fieldName2)
	{
		if( !($retour = StringUtils::equals($form->getField($fieldName1)->value(), $form->getField($fieldName2)->value())) ){
			$form->getField($fieldName1)->setWarningMessage();
			$form->getField($fieldName2)->setWarningMessage();
		}

		return $retour;
	}
}
