<?php
namespace Api\CatalogModule\Controller;

use Novice\BackController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CatalogController extends BackController
{
	/**
	 * @Route("/catalog", name="api_catalog_index")
	 */
	public function executeIndex(Request $request)
	{	
		/*$this->assign(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));*/

		$res = new JsonResponse(array('greetings' => 'Hello World !',
							'saludos' => 'Buenos días'));
		$res->setCharset('UTF-8');

		return $res;
	}
}
