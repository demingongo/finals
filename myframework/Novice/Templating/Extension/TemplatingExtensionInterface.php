<?php
namespace Novice\Templating\Extension;

interface TemplatingExtensionInterface
{
	public function getType();

	public function getName();

	public function getCallback();
}