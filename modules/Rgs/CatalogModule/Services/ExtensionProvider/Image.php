<?php

namespace Rgs\CatalogModule\Services\ExtensionProvider;

use Novice\Form\Extension\ExtensionProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Image implements ExtensionProvider
{
	
	/**
     * @var ContainerInterface
     */
    private $container;

    private $extension;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
        $options = array(
			'name' => 'image',
			'type' => 1,
			'label' => "Image",
			//'input_attributes' => array("required" => "required"),
			'text_remove_btn' => '&times;',
			'akeys' => array(md5('one'), md5('gfk'), md5('theodore')),
		);
        $this->extension = new \Novice\Form\Extension\Filemanager\FilemanagerExtension('/plugins/filemanager/filemanager', $options);
    }

    public function getExtension(){
        return $this->extension;
    }
}
