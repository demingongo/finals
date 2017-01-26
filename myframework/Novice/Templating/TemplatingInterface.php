<?php
namespace Novice\Templating;

/**
 * TemplatingInterface is the interface implemented by service templating classes.
 *
 */
interface TemplatingInterface
{
	public function setContentFile($contentFile);

	public function getContentFile();

	public function templateExists($resource_name);

	public function assign($varname, $var = null, $nocache = false);

	public function getGeneratedPage();

	/**
	  *
	  * see fetch method in 'Smarty_Internal_TemplateBase' => $vendorDir . '/smarty/smarty/libs/sysplugins/smarty_internal_templatebase.php'
	  *
	  */
	public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null/*, $display = false, $merge_tpl_vars = true, $no_output_filter = false*/);

	public function addExtension(Extension\TemplatingExtensionInterface $extension);
}