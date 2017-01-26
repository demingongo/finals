<?php
namespace Novice\Templating\Extension;

abstract class ModifierExtension extends AbstractExtension
{
	public function execute($string){
	}

	/**
     * {@inheritDoc}
     */
	public function getType(){
		return 'modifier';
	}
}