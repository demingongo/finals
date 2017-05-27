<?php
namespace Rgs\CatalogModule\Services;

use Novice\Templating\TemplatingInterface;

use Rgs\UserModule\Entity\User;

class Mailer
{
	protected $mailer;
	protected $templating;
	
	private $from = "retrogamessystem@hotmail.be";
	private $name = "Retrogames System";

	private $noReplyFrom = "no-reply@retrogamessystem.be";
	private $noReplyName = "no-reply";

	public function __construct($mailer, TemplatingInterface $templating)
	{
		$this->mailer = $mailer;
		$this->templating = $templating;
	}

	protected function sendMessage($to, $subject, $body)
	{
		$message = \Swift_Message::newInstance();
		$message->setFrom($this->from,$this->name)
		->setTo($to)
		->setSubject($subject)
		->setBody($body,'text/html') ;
		$this->mailer->send($message);
	}

	protected function setAssign()
	{
		$this->templating->assign(
			array(
			'from' => $this->from,
			'name' => $this->name,
			));
	}

	public function sendCustom($to, $subject, $body)
	{
		$this->send($to, $subject, $body);
	}

	public function send($to, $subject, $body)
	{
		$this->setAssign();
		$this->sendMessage($to, $subject, $body);
	}

	public function sendTest()
	{
		$this->setAssign();
		$subject = "Est-ce que ça marche?";
		$template = 'file:tests/mailTest.tpl';
		$to = 'sdemingongo@gmail.com';
		$this->templating->assign(array('tpl_name' => $template));
		$body = $this->templating->fetch($template);
		$this->sendMessage($to, $subject, $body);
	}

	public function sendRegisterConfirm(User $user, $link)
	{
		$this->setAssign();
		$subject = $this->name." : confirmation de l'ouverture du compte";
		$template = 'file:[RgsCatalogModule]Mail/mail.php';
		$to = $user->getEmail();//'sdemingongo@gmail.com';
		$msg = "Bonjour,<br /><br />

La création d'un compte pour ".$this->name." a été demandée en utilisant votre adresse de courriel. Pour confirmer votre enregistrement, veuillez visiter la page web suivante : <br /><br />

<a href='".$link."'>".$link."</a><br /><br />

Dans la plupart des logiciels de courriel, cette adresse est un lien actif qu'il vous suffit de cliquer. Si cela ne fonctionne pas, copiez ce lien et collez-le dans la barre d'adresse de votre navigateur web.<br /><br />

Si vous avez besoin d'aide, veuillez contacter l'administrateur du site.";
		$this->templating->assign(array('tpl_name' => $template, 'message' => $msg));
		$body = $this->templating->fetch($template);
		$this->sendMessage($to, $subject, $body);
	}
	
	public function sendLostPasswordConfirm(User $user, $link)
	{
		$this->setAssign();
		$subject = $this->name." : reset password";
		$template = 'file:[RgsCatalogModule]Mail/mail.php';
		$to = $user->getEmail();
		$msg = "Hello,<br /><br />

There was a request to reset password for ".$this->name.". Click on the link to confirm : <br /><br />

<a href='".$link."'>".$link."</a><br /><br />

Dans la plupart des logiciels de courriel, cette adresse est un lien actif qu'il vous suffit de cliquer. Si cela ne fonctionne pas, copiez ce lien et collez-le dans la barre d'adresse de votre navigateur web.<br /><br />

Si vous avez besoin d'aide, veuillez contacter l'administrateur du site.";
		$this->templating->assign(array('tpl_name' => $template, 'message' => $msg));
		$body = $this->templating->fetch($template);
		$this->sendMessage($to, $subject, $body);
	}

	public function sendRequestConfirm(\Rgs\CatalogModule\Entity\UserRequest $userRequest)
	{
		$this->setAssign();
		$subject = $this->name." : RE - ".$userRequest->getSubject();
		$template = 'file:[RgsCatalogModule]Mail/mail.php';
		$to = $userRequest->getUser()->getEmail();
		$msg = "Hello,<br /><br />

We read your request and we'll be grateful to receive you during our open hours with your product so we can continue further the negociations.";
		$this->templating->assign(array('tpl_name' => $template, 'message' => $msg));
		$body = $this->templating->fetch($template);
		$this->sendMessage($to, $subject, $body);
	}
}
