<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {setcookie} function plugin
 * Type:     function<br>
 * Name:     setcookie<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return void
 */

function smarty_function_setcookie($params, &$smarty)
{
	if(isset($params['name'])){
		if(!isset($params['value']))
		$params['value'] = '';
		if(!isset($params['expire']))
		$params['expire'] = 0;
		if(!isset($params['path']))
		$params['path'] = '/';
		if(!isset($params['domain']))
		$params['domain'] = null;
		if(!isset($params['secure']))
		$params['secure'] = false;
		if(!isset($params['httpOnly']))
		$params['httpOnly'] = true;
		if(class_exists('Symfony\Component\HttpFoundation\Cookie')){
			try{
				$cookie = \Symfony\Component\HttpFoundation\Cookie($params['name'], $params['value'], $params['expire'], $params['path'], $params['domain'], $params['secure'], $params['httpOnly']);
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			catch(\Exception $e){
				trigger_error($e->getMessage());
			}
		}
		else{
			setcookie($params['name'], $params['value'], $params['expire'], $params['path'], $params['domain'], $params['secure'], $params['httpOnly']);
		}
	}
}
?>