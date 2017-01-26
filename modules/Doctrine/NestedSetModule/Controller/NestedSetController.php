<?php
namespace Doctrine\NestedSetModule\Controller;

use Novice\HTTPRequest;

class NestedSetController extends \Novice\BackController
{
	public function executeIndex(/*HTTPRequest $request*/)
	{	
		$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
	}
}
