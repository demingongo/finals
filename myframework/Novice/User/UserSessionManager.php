<?php
namespace Novice\User;

use Novice\Session\Session;

use Symfony\Component\HttpFoundation\Cookie;

class UserSessionManager
{
  protected $session;

  protected $idName = 'novice_user_session_manager_id';

  protected $cookieIdName = 'novice_auth_token_selector';

  protected $cookieTokenName = 'novice_auth_token';

  private $cookieId;

  private $cookieToken;

  public function __construct(Session $session)
  {
		  $this->session = $session;
		  return $this;
  }

  public function getSession()
  {
	  return $this->session;
  }

  public function getId()
  {
	  return $this->session->get($this->idName);
  }

  public function getCookieId()
  {
	  return $this->cookieId;
  }

  public function getCookieToken()
  {
	  return $this->cookieToken;
  }

  public function hasCookie()
  {
	  return ($this->cookieId && $this->cookieToken);
  }

  /*public function logIn($id)
  {
	  return $this->login($id);;
  }

  public function logOut()
  {
	  return $this->logout();
  }*/

  public function login($id)
  {
	  $this->session->set($this->idName, $id);
	  $this->session->setAuthenticated(true);
	  return $this;
  }

  public function logout()
  {
	  /*$this->session->setAuthenticated(false);
	  $this->session->remove($this->idName);*/
	  $this->session->invalidate();
	  return $this;
  }

  public function createLoginCookie(\Symfony\Component\HttpFoundation\Response $response, $id, $token, $expire, $path = '/', $domain = null, $secure = false, $httpOnly = true)
  {
		  $cookieId = new Cookie($this->cookieIdName, $id, $expire, $path, $domain, $secure, $httpOnly);
		  $cookieToken = new Cookie($this->cookieTokenName, $token, $expire, $path, $domain, $secure, $httpOnly);
		  $response->headers->setCookie($cookieId);
		  $response->headers->setCookie($cookieToken);

	  return $this;
  }

  public function retrieveLoginCookie(\Symfony\Component\HttpFoundation\Request $request)
  {
	  if($request->cookies->has($this->cookieIdName) && $request->cookies->has($this->cookieTokenName)){
			$this->cookieId = $request->cookies->get($this->cookieIdName);
			$this->cookieToken = $request->cookies->get($this->cookieTokenName);
	  }
	  return $this;
  }

  public function clearLoginCookie(\Symfony\Component\HttpFoundation\Response $response, $path = '/', $domain = null, $secure = false, $httpOnly = true)
  {
	  $response->headers->clearCookie($this->cookieIdName, $path = '/', $domain = null, $secure = false, $httpOnly = true);
	  $response->headers->clearCookie($this->cookieTokenName, $path = '/', $domain = null, $secure = false, $httpOnly = true);
  }

  public function isAuthenticated()
  {
    return $this->session->isAuthenticated();
  }

  public function setAuthenticated($authenticated)
  {
	$this->session->setAuthenticated($authenticated);

	return $this;
  }
}