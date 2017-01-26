<?php
namespace Novice\Module\SwiftmailerModule\Controller;

use Symfony\Component\HttpFoundation\Request;

class SwiftmailerController extends \Novice\BackController
{
	public function executeIndex(Request $request)
	{	
		$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}
}
