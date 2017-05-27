<?php

namespace Rgs\CatalogModule\Services;

use Novice\Annotation\Editor\PropertyEditorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleNameEditor implements PropertyEditorInterface
{
	
	/**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
	
	
	/**
     * @param string   			 	 $propertyName
	 * @param string|numeric|array   $propertyValue
     * @param object   			 	 $attribute
	 *
	 * @return void
	 */
	public function edit($propertyName, $propertyValue, $attribute)
	{
		if($propertyName == 'name'){
			$attribute->setName(strtoupper($propertyValue));
			return;
		}
		
		$c = $this->container->get('managers')->getManager()->getRepository("RgsCatalogModule:Category")->findOneById($propertyValue);
		
		$attribute->setCategory($c);
	}
	
	/**
	 * Returns an array of classes names to be edited by this editor or void for all classes
	 * (ex: return array("Namespace1\ClassName1", "Namespace2\ClassName2"); )
	 *
	 *
	 * @return array()|void
	 */
	public function editableClasses()
	{
		//return array("Rgs\CatalogModule\Entity\Article");
		//return array("Article");
	}
	
	/**
	 * Returns an array of properties names to be edited by this editor
	 * (ex: return array("articles", "children"); )
	 *
	 *
	 * @return array()
	 */
	public function editableProperties()
	{
		return array("category","name");
	}
}
