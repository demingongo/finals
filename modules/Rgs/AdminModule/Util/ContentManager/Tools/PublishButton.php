<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

use Rgs\CatalogModule\Entity\Model\PublishedInterface;

class PublishButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct($cm, [
            'item_action' => true,
            'value' => 'publish',
            'label' => 'Publish',
            'icon' => 'fa fa-check-circle text-success'
        ]);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $container = $cm->getContainer();
        $entityName = $cm->getEntityName();
        $result = $container->get('managers')->getManager()->getRepository($entityName)
						->publish($ids, PublishedInterface::PUBLISHED);
    }
}