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
	
	$request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
	$router = $smarty->getContainer()->get('router');
	$qs = "";
	
	/*if (null !== $qs = $request->getQueryString()) {
        $qs = '?'.$qs;
    }*/

	$attributes = $router->matchRequest($request);
	
	$route_id = $attributes["_route"];
	
	unset($attributes["_route"]);	
	if(isset($attributes["_controller"]))
		unset($attributes["_controller"]);

	if(empty($params['pageActuelle']) && isset($attributes["_page"]))
	{
		$params['pageActuelle'] = $attributes["_page"] == 0 ? 1 : $attributes["_page"];
	}
		
	if(empty($params['nbPages']) && isset($params["paginator"]))
	{
		if(is_object($params["paginator"]) && $params["paginator"] instanceof \Doctrine\ORM\Tools\Pagination\Paginator){
			if(null != $_limit = $params['paginator']->getQuery()->getMaxResults())
				$params['nbPages'] = ceil(count($params["paginator"]) / $_limit);
		}
			
		unset($params["paginator"]);	
	}
	
		
	if (empty($params['pageActuelle']) || !isset($params['nbPages'])) {
        //$smarty->_trigger_fatal_error("[plugin] paramètre vide");
		return "[<b>plugin</b>&nbsp;pagination&nbsp;:&nbsp;<b>params</b>&nbsp;pageActuelle&nbsp;,&nbsp;nbPages]";
	}

	
	$qVars = $request->query->all();
	$countQueryVars = count($qVars);
	$queryStrict = isset($params['queryStrict']) && is_array($params['queryStrict']) ? $params['queryStrict'] : array();
	$noQuery = isset($params['noQuery']) ? (bool)$params['noQuery'] : false;
	
	if(!$noQuery){
	$hasQueryStrict = count($queryStrict) > 0;
	if($hasQueryStrict){
		$newQVars = array();
		$countQueryVars = 0;
		foreach($queryStrict as $v){
			if(isset($qVars[$v])){
				if(!isset($attributes[$v])){
					$attributes[$v] = $qVars[$v];
				}
				else{
					$newQVars[$v] = $qVars[$v];
				}
				$countQueryVars++;
			}
		}
		$qVars = $newQVars;
	}
	else{
		foreach($qVars as $k => $v){
			if(!isset($attributes[$k])){
				$attributes[$k] = $v;
				unset($qVars[$k]);
			}
		}
	}
	
	if(count($qVars) > 0){
		if(count($qVars) == $countQueryVars){
			if($hasQueryStrict){
				foreach($qVars as $k => $v){
					$qs .= '&' . $k . '=' . $v;
				}
				$qs = '?' . substr($qs, 1);
			}
			else{
				$qs = '?' . $request->getQueryString();
			}
		}
		else{
			foreach($qVars as $k => $v){
				$qs .= '&' . $k . '=' . $v;
			}
		}
	}
	}

	$pA = $params['pageActuelle'];
	$nbP = $params['nbPages'];
	$max = floor(8/2);
	$referenceType = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH;
	
	if($pA == 0 || $nbP < 1){
		return;
	}
	if($pA > $nbP){
		$pA = $nbP;
	}

	if(isset($params['max']) && is_numeric($params['max'])){
			$max = floor($params['max']/2);
			unset($params['max']);
	}

	if(isset($params['referenceType'])){
			$referenceType = $params['referenceType'];
			unset($params['referenceType']);
	}

$array = array();

$datapage = (isset($params['data-page']) && $params['data-page']);

try{

	$array[]="<ul class='pagination pagination-sm'>";
	
	//if($nbP > 1)
	if($pA > 1){
		if($datapage)
			$array[]="<li><a href='#' aria-label='Previous' data-page='".($pA - 1)."'>
		<span aria-hidden='true'>&laquo;</span></a></li>";
		else
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
	
	if($nbP - $pA < $max){		
		$max = $max*2 - (($nbP - ($pA)+1));
	}
	
	$i =$pA - $max;
	
	if($i > 1){
		$attributes['_page'] = $i - 1;
		$array[] = "<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' >...</a></li>";
	}
	
	while($i < $pA){
		if($i >= 1){
			$attributes['_page'] = $i;
			$array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."'>".$i."</a></li>";
		}
		else{
			$max++;
		}
		$i++;
	}

	for($i=$pA; $i<=$nbP && $i < $pA + $max; $i++)
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
			 }
	     }
	}
	
	if($i <= $nbP){
		$attributes['_page'] = $i;
		$array[] = "<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' >...</a></li>";
	}


	if($pA < $nbP){
		if($datapage)
			$array[]="<li><a href='#' aria-label='Next' data-page='".($pA + 1)."''><span aria-hidden='true' class='glyphicon glyphicon-forward'></span></a></li>";
		else{
			$attributes['_page'] = $pA + 1;
			$array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' aria-label='Next'>
			<span aria-hidden='true' class='glyphicon glyphicon-forward'></span></a></li>";
			$attributes['_page'] = $nbP;
			$array[]="<li><a href='". $router->generate($route_id,$attributes,$referenceType) . $qs ."' aria-label='Last page'>
			<span aria-hidden='true' class='glyphicon glyphicon-fast-forward'></span></a></li>";
		}
	}
	else{
		$attributes['_page'] = $pA + 1;
			$array[]="<li class='disabled'><a aria-label='Next'>
			<span aria-hidden='true' class='glyphicon glyphicon-forward'></span></a></li>";
			$attributes['_page'] = $nbP;
			$array[]="<li class='disabled'><a aria-label='Last page'>
			<span aria-hidden='true' class='glyphicon glyphicon-fast-forward'></span></a></li>";
	}
	$array[]="</ul>";
}
catch(\Symfony\Component\Routing\Exception\ExceptionInterface $e){
	trigger_error($e->getMessage());
}

return implode(" ",$array );

}
?>