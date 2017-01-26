<?php
namespace Novice\Templating\Extension;

abstract class BlockExtension extends AbstractExtension
{
	public function execute($params, $content, $template, &$repeat){
	}

	/**
     * {@inheritDoc}
     */
	public function getType(){
		return 'block';
	}
}