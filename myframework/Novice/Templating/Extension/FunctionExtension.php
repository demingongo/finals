<?php
namespace Novice\Templating\Extension;

abstract class FunctionExtension extends AbstractExtension
{
	public function execute($params, $template){
	}

	/**
     * {@inheritDoc}
     */
	public function getType(){
		return 'function';
	}
}