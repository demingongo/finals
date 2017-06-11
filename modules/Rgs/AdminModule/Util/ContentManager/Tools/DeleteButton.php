<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class DeleteButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
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
        ], $cm);
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
                    $em->getConnection()->beginTransaction();
					try{
						foreach($ids as $id){
							$result = $em->getRepository($entityName)
										->findOneById($id);
							$em->remove($result);
						}
						$em->flush();
						$em->getConnection()->commit();
						$cm->getContainer()->get('session')->getFlashBag()->set('success', 'Successfully deleted');
					}
					catch(\Exception $e){
						$em->close();
						$em->getConnection()->rollback();
						$cm->getContainer()->get('session')->getFlashBag()->set('error', '<b>Failure occured</b>');
					}
					/*$result = $em->getRepository($entityName)
					->deleteByIds($ids);*/
				}
		}
		catch(\Exception $e){
		        $cm->getContainer()->get('session')->getFlashBag()->set('error', $e->getMessage());
		}
    }
}