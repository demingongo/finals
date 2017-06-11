<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

use Rgs\CatalogModule\Entity\Model\PublishedInterface;

class UnpublishButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
            'item_action' => true,
            'value' => 'unpublish',
            'label' => 'Unpublish',
            'icon' => 'fa fa-times-circle text-danger'
        ], $cm);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $container = $cm->getContainer();
        $entityName = $cm->getEntityName();
        $result = $container->get('managers')->getManager()->getRepository($entityName)
						->publish($ids, PublishedInterface::NOT_PUBLISHED);
    }
}