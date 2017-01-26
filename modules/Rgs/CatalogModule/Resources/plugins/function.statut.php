<?php


function smarty_function_statut($params, &$smarty)
{	
	if (!isset($params['statut'])) {
        //trigger_error("[plugin] le paramètre 'statut' est vide");
        return;
	}

	$alt = "";
	$title = "";
	$src = "";

switch ($params['statut'])
    { 
        case \Rgs\CatalogModule\Entity\Model\PublishedInterface::PUBLISHED :
		case true :
			$alt = "published";
			$title = "published";
			$src = $smarty->getAssets()->getUrl('/img/pictos/Ok-16.png', null);
			//return '<img alt="published" src="'.$smarty->getAssets()->getUrl('/img/pictos/publish_y.png', null).'" title="published" />';
			break;

        case \Rgs\CatalogModule\Entity\Model\PublishedInterface::NOT_PUBLISHED :
		case false :
			$alt = "not published";
			$title = "not published";
			$src = $smarty->getAssets()->getUrl('/img/pictos/Cancel_2-16.png', null);
			//return '<img alt="not published" src="'.$smarty->getAssets()->getUrl('/img/pictos/publish_n.png', null).'" title="not published" />';
			break;
	}

	if(isset($params['srconly']) && $params['srconly'] == true){
		return $src;
	}

	return '<img alt="'.$alt.'" src="'.$src.'" title="'.$title.'" />';
}

?>