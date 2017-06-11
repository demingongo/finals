<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class CancelReservationButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
            'type' => 'warning',
            'item_action' => true,
            'value' => 'cancel',
            'label' => 'Cancel',
            'icon' => 'fa fa-trash',
            'attributes' => [
                'data-novice-toggle' => "confirm", 
			    'data-novice-text' => "Cancel selected items ?
Warning: This action cannot be undone."
            ]
        ], $cm);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $entityName = $cm->getEntityName();
        $em = $cm->getContainer()->get('managers')->getManager();
        try{
				$result = $em->getRepository($entityName)
						->cancelByIds($ids);
				
				$cm->getContainer()->get('session')->getFlashBag()->set('success', "Successfully canceled");
		}
		catch(\Exception $e){
		        $cm->getContainer()->get('session')->getFlashBag()->set('error', $e->getMessage());
		}
    }
}