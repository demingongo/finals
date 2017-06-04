<?php
namespace Novice\Templating\Extension;

/**
 * Smarty plugin
 */

/**
 * Smarty {img} function plugin
 * Type:     function<br>
 * Name:     img<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return url|null
 */

class ImgFunctionExtension extends FunctionExtension
{
	public function execute($params, $template){
		$html = '<img';
	
		$src = "";
		$fallbackSrc = "";
		$path = "";
		$fallback = "";
		$package = null;

		if(isset($params['src'])){
			$path = $params['src'];
			unset( $params['src']);
		}
		if(isset($params['package'])){
			$package = $params['package'];
			unset( $params['package']);
		}

		if($template->getContainer()->hasParameter('templating.plugin.img.fallback')){
			$fallback = $template->getContainer()->getParameter('templating.plugin.img.fallback');
		}
		if(isset($params['fallback'])){
			$fallback = $params['fallback'];
			unset( $params['fallback']);
		}
		if(isset($params['data-fallback-src']) && !empty($params['data-fallback-src'])){
			$fallback = "";
		}

		if(empty($path) && $template->getContainer()->hasParameter('templating.plugin.img.empty'))
		{
			$path = $template->getContainer()->getParameter('templating.plugin.img.empty');
			$src = $template->getAssets()->getUrl($path, null);
		}
		else
		{
			try{
				$src = $template->getAssets()->getUrl($path, $package);
			}
			catch(\Exception $e){
				$src = $template->getAssets()->getUrl($fallback, null);
			}
		}
		
		if(!empty($fallback)){
			$fallbackSrc = $template->getAssets()->getUrl($fallback, null);
		}

		$html .= ' src="'.$src.'"';
		$html .= !empty($fallbackSrc) ? ' data-fallback-src="'.$fallbackSrc.'"' : '';

		foreach($params as $k => $v)
		{
			$html .= ' '.$k.'="'.$v.'"';
		}

		$html .= ' />';

		return $html;
	}

	/**
     * {@inheritDoc}
     */
	public function getType(){
		return 'function';
	}

	/**
     * {@inheritDoc}
     */
	public function getName(){
		return 'img';
	}
}