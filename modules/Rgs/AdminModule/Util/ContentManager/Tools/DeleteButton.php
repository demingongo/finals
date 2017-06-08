<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class DeleteButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct($cm, [
            'type' => 'danger',
            'item_action' => true,
            'value' => 'delete',
            'label' => 'Delete',
            'icon' => 'fa fa-trash',
            'attributes' => [
                'data-novice-toggle' => "confirm", 
			    'data-novice-text' => "Delete selected items ?
Warning: This action cannot be undone."
            ]
        ]);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $entityName = $cm->getEntityName();
        $em = $cm->getContainer()->get('managers')->getManager();
        try{
				if($cm->getName() == 'category'){
					$result = $em->getRepository($entityName)
					->deleteByIds($cm->getContainer()->get('nested_set'), $ids);
				}
				else{
					$result = $em->getRepository($entityName)
					->deleteByIds($ids);
				}
		}
		catch(\Exception $e){
		        $this->get('session')->getFlashBag()->set('error', $e->getMessage());
		}
    }
}