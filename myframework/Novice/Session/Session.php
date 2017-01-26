<?php
namespace Novice\Session;

use Symfony\Component\HttpFoundation\Session\Session as BaseSession;

/**
**Assigner un attribut à l'utilisateur.
**Obtenir la valeur d'un attribut.
**Authentifier l'utilisateur.
**Savoir si l'utilisateur est authentifié.
**Assigner un message informatif à l'utilisateur que l'on affichera sur la page.
**Savoir si l'utilisateur a un tel message.
**Récupérer ce message.
**/

class Session extends BaseSession
{

  /*public function setUser(Entity $user)
  {
	  //if(!$this->has('user')){
		  $this->set('user', $user);
	  //}

	  return $this;
  }

  public function getUser()
  {
	return $this->get('user');
  }*/

  public function isAuthenticated()
  {
    return $this->has('auth') && $this->get('auth') === true;
  }

  public function setAuthenticated($authenticated = true)
  {
    if (!is_bool($authenticated))
    {
      throw new \InvalidArgumentException('La valeur spécifiée à la méthode User::setAuthenticated() doit être un boolean');
    }
	$this->set('auth', $authenticated);

	return $this;
  }
}