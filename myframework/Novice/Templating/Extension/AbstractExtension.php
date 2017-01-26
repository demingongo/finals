<?php
namespace Novice\Templating\Extension;

abstract class AbstractExtension implements TemplatingExtensionInterface
{
	/**
     * {@inheritDoc}
     */
	public function getCallback(){
		return array($this, 'execute');
	}
}