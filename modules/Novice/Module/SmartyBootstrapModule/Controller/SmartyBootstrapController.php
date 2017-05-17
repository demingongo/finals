<?php
namespace Novice\Module\SmartyBootstrapModule\Controller;

use Novice\BackController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SmartyBootstrapController extends BackController
{
	/**
	 * @Route("/", name="novice_module_smarty_bootstrap_index")
	 */
	public function executeIndex(Request $request)
	{	
		$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}
}
