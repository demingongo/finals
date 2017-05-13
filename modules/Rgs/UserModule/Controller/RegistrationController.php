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

use Rgs\UserModule\Util\UserModuleUtils;

class RegistrationController extends \Novice\BackController
{

	public function executeRegister(Request $request)
	{

		$this->setView("file:[UserModule]Registration/register.php");

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
		
		/*dump(stream_get_transports());
		
		dump(__METHOD__);
		//dump($this->get('swiftmailer.transport'));
		try{
		$this->get('rgs.mailer')->sendCustom("sdemingongo@gmail.com","Est-ce que ça marche?","Hey John");
		}
		catch(\Exception $e){
			dump($this->get('swiftmailer.transport'));
			throw $e;
		}
		dump($this->get('swiftmailer.transport'));
		dump($this->get('swiftmailer.mailer'));
		exit(__METHOD__);*/


		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{	

			if($form->isValid() && $user->isValid()) {
				
				$utils = new UserModuleUtils();
				
				if($utils->compareFormFields($form, 'password', 'confirm'))
				{
					try{
						$user->setPassword(Password::hash($user->getPassword()));
						$confirmationToken = $utils->createRandomToken($generator);
						$user->setConfirmationToken(Password::hash($confirmationToken));
						$group = $em->getRepository('UserModule:Group')->findOneByName('Client');
						$user->setGroup($group);
						$em->persist($user);
						$em->flush();

						
						$this->get('rgs.mailer')->sendRegisterConfirm($user, 
							$this->generateUrl('user_registration_confirm',array(
							'token' => $confirmationToken,
							'slug' => $user->getSlug(),
						), true));

						$em->getConnection()->commit();

						$session->getFlashBag()->set('success','User validé, veuillez confirmez l\'enrgistrement de votre compte par email');
						return $this->redirect($this->generateUrl('rgs_catalog_index',array()));
					}
					catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
						$em->close();
						$em->getConnection()->rollback();
						if(count($em->getRepository('UserModule:User')->findByLogin($user->getLogin())) > 0){
							$form->getField('login')->setWarningMessage(': "'.$user->getLogin().'" est indisponible, veuillez changer de login !');
						}
						if(count($em->getRepository('UserModule:User')->findByEmail($user->getEmail())) > 0){
							$form->getField('email')->setWarningMessage(': Cette adresse mail est déjà enregistrée, veuillez changer !');
						}
					}
				}
			}
		}
		catch(\Exception $e){
			$em->close();
			$em->getConnection()->rollback();
			if($e instanceof \Novice\Form\Exception\SecurityException){
				$session->getFlashBag()->set('notice', '<b>Failure occured</b>, <a href="'.$this->generateUrl('user_registration_register', array(), true).'" class="alert-link">fill in the form</a> and try submitting again.');
			}
			else{
				throw $e;
			}
		}

		$this->assign('form', $form->createView());
	}

	public function executeConfirm(Request $request)
	{
		$this->setView("file:[UserModule]Registration/confirm.php");

		$session = $this->get('session');

		$slug = $request->attributes->get('slug');
		$token = $request->attributes->get('token');

		$message = 'Données de confirmation non valides.';
		
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserModule:User')->findOneBySlug($slug);
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
	}
}
