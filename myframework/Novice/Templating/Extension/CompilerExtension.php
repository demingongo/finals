<?php
namespace Novice\Templating\Extension;

abstract class CompilerExtension extends AbstractExtension
{
	public function execute($params, $smarty){
	}

	/**
     * {@inheritDoc}
     */
	public function getType(){
		return 'compiler';
	}
}