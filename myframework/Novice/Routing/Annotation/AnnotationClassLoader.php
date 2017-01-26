<?php

namespace Novice\Routing\Annotation;

use Symfony\Component\Routing\Route,
	Symfony\Component\Routing\Loader\AnnotationClassLoader as AbstractAnnotationClassLoader;


class AnnotationClassLoader extends AbstractAnnotationClassLoader
{
	protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot){
		//echo "<p>configuring route";
		
		$methodName = $method->getName();
		
		$controller = $method->class."::".$methodName;
		/*dump($class);
		dump($method);
		dump($controller);
		exit();
		dump($controller);*/
		$route->addDefaults(array( "_controller" => $controller ));
	}
}