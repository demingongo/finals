<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class CancelAllReservationButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
            'type' => 'danger',
            'item_action' => true,
            'value' => 'cancelAll',
            'label' => 'Cancel all',
            'icon' => 'fa fa-trash',
            'attributes' => [
                'data-novice-toggle' => "confirm", 
			    'data-novice-text' => "Delete selected items ?
Warning: This action cannot be undone."
            ]
        ], $cm);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $entityName = $cm->getEntityName();
        $stateAttribute = $cm->getContainer()->get('request_stack')->getCurrentRequest()->attributes->get('state');

        switch($itemType)
		{
				case 'expired':
					$cancelAllFn = 'cancelExpired';
					break;
				case 'reservations':
					$cancelAllFn = 'cancelNonExpired';
					break;
				default:
					throw new \InvalidArgumentException(
                        'Missing a "state" attribute in request for '.__METHOD__.'. It must be string: \'expired\' or \'reservations\''
                        );
					return;
		}
        
        $em = $cm->getContainer()->get('managers')->getManager();
        return $result = $em->getRepository($entityName)
						->$cancelAllFn();
    }
}