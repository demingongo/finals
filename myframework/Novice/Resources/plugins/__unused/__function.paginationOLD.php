<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {pagination} function plugin
 * Type:     function<br>
 * Name:     pagination<br>
 * Purpose:  pagination in template
 *
 * Params:
 * <pre>
 * - pageActuelle   - integer 
 * - nbPages        - integer
 * </pre>
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return string
 */

function smarty_function_pagination($params, &$smarty)
{
	
	$request = $smarty->getContainer()->get('request');
	$router = $smarty->getContainer()->get('router');
		
	if (null !== $qs = $request->getQueryString()) {
        $qs = '?'.$qs;
    }

	$attributes = $router->matchRequest($request);
	
	$route_id = $attributes["_route"];
	
	unset($attributes["_route"]);	
	if(isset($attributes["_controller"]))
		unset($attributes["_controller"]);

	if(empty($params['pageActuelle']) && !empty($attributes["_page"]))
		$params['pageActuelle'] = $attributes["_page"];
	
		
	if (empty($params['pageActuelle']) || empty($params['nbPages'])) {
        //$smarty->_trigger_fatal_error("[plugin] paramètre vide");
		return "[<b>plugin</b>&nbsp;pagination&nbsp;:&nbsp;<b>params</b>&nbsp;pageActuelle&nbsp;,&nbsp;nbPages]";
	}

	

	$pA = $params['pageActuelle'];
	$nbP = $params['nbPages'];
	$referenceType = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH;

	if(isset($params['referenceType'])){
			$referenceType = $params['referenceType'];
			unset($params['referenceType']);
	}

//position du 'p' de 'page=' dans l'URI
$pos = strrpos($_SERVER["REQUEST_URI"],"page=");

/**SI 'page=' pas dans URI, y rajouter '-page='
  *SINON uniquement remplacer la valeur de page dans URI
  *pour les liens de pagination
  */
if($pos==false){
	$URI = $_SERVER['REQUEST_URI']."-page=";
	$debutNumPage = strlen($URI);
	$finNumPage = $debutNumPage;
}
else{
	$URI = $_SERVER["REQUEST_URI"];
	$debutNumPage = $pos+5;
	$finNumPage = $pos+5;
	while(isset($URI{$finNumPage}) && is_numeric($URI{$finNumPage})){
		$finNumPage++;
	}
	$finNumPage--;
}

$array = array();

$datapage = (isset($params['data-page']) && $params['data-page']);

try{
	//$url = $smarty->getContainer()->get('router')->generate($route_id,$attributes,$referenceType);

	$array[]="<ul class='pagination pagination-sm'>";
	if($pA > 1){
		if($datapage)
			$array[]="<li><a href='#' aria-label='Previous' data-page='".($pA - 1)."'>
		<span aria-hidden='true'>&laquo;</span></a></li>";
		else
			//$array[]="<li><a href='". substr_replace($URI, $pA - 1, $debutNumPage, $finNumPage) ."' aria-label='Previous' >
			$attributes['_page'] = 1;
			$array[]="<li>
	<a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' aria-label='First page'><span class='glyphicon glyphicon-fast-backward'></span></a>
	</li>";
			$attributes['_page'] = $pA - 1;
			$array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' aria-label='Previous' >
			<span aria-hidden='true' class='glyphicon glyphicon-backward'></span></a></li>";
	}
	else{
		$array[]="<li class='disabled'>
	<a aria-label='First page'><span class='glyphicon glyphicon-fast-backward'></span></a>
	</li>
	<li class='disabled'><a aria-label='Previous'><span aria-hidden='true' class='glyphicon glyphicon-backward'></span></a></li>";
	}

	for($i=1; $i<=$nbP; $i++)
	{
	     if($i==$pA)
	     {
	        $array[]="<li class='active'><a><b>".$i."</b> <span class='sr-only'>(current)</span></a></li>"; 
	     }	
	     else
	     {
			 if($datapage)
				$array[]="<li><a href='#' data-page='".$i."'>".$i."</a></li>";
			 else{
				 $attributes['_page'] = $i;
				 $array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."'>".$i."</a></li>";
				// $array[]="<li><a href='".substr_replace($URI, $i, $debutNumPage, $finNumPage)."'>".$i."</a></li>";
			 }
	     }
	}

	if($pA < $nbP){
		if($datapage)
			$array[]="<li><a href='#' aria-label='Next' data-page='".($pA + 1)."''><span aria-hidden='true' class='glyphicon glyphicon-forward'></span></a></li>";
		else{
			//$array[]="<li><a href='". substr_replace($URI, $pA + 1, $debutNumPage, $finNumPage) ."' aria-label='Next'>
			$attributes['_page'] = $pA + 1;
			$array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' aria-label='Next'>
			<span aria-hidden='true' class='glyphicon glyphicon-forward'></span></a></li>";
		}
	}
	$array[]="</ul>";
}
catch(\Symfony\Component\Routing\Exception\ExceptionInterface $e){
	trigger_error($e->getMessage());
}

return implode("&nbsp;",$array );

/***
 ****ANCIENNE FAçON DE FAIRE LA PAGINATION****
 ***DEFAUT : efface ce qu'il y a dans l'URI à partir de '-page='
 ***mais ne réécrit que '-page=value'

$pos = strrpos($_SERVER["REQUEST_URI"],"-page=");
$PageName = substr($_SERVER['REQUEST_URI'],0, $pos);
$array = array();
if($params['PageActuelle'] > 1){
$array[]="<a href='". $PageName ."-page=".($params['PageActuelle'] - 1)."'>&lt;</a>";
}
for($i=1; $i<=$params['NbPages']; $i++)
{
     if($i==$params['PageActuelle'])
     {
        $array[]="<b>".$i."</b>"; 
     }	
     else
     {
        $array[]="<a href='". $PageName ."-page=".$i."'>".$i."</a>";
     }
}
if($params['PageActuelle'] < $params['NbPages']){
$array[]="<a href='". $PageName ."-page=".($params['PageActuelle'] + 1)."'>&gt;</a>";
}
return implode("&nbsp;",$array );*/
}
?>