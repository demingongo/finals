<?php
/**
 * Smarty plugin
 */

/**
 * Smarty auth block plugin
 *
 * @author Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Template $template template object
 * @param boolean                  &$repeat  repeat flag
 *
 * @return  html http links to urls rel css
 */

function smarty_block_auth($params, $content, &$smarty, &$repeat)
{
	// n'affiche que lors de la balise fermante
	if(!$repeat){
		if(!$smarty->getContainer()->get('session')->isAuthenticated()){
			return;
		}
		
		if(!empty($params)){
			$user = $smarty->getContainer()->get('app.user')->getData();
			
			//permissions
			if(!empty($params['permissions'])){
				$permissions = $params['permissions'];
				if(!is_array($permissions)){
					$permissions = array($permissions);
				}
				
				//allow access if user has one of the roles
				$hasPermission = false;
				foreach($permissions as $role){
					if($user->hasRole($role)){
						$hasPermission = true;
						break;
					}
				}

				if(!$hasPermission){
					return;
				}
			}
		}

		return $content;

		
	}
}
?>