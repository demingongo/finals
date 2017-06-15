<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

use Symfony\Component\HttpFoundation\RedirectResponse;

class UserRequestValidButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
            'item_action' => true,
            'value' => 'accept',
            'label' => 'Accept',
            'icon' => 'fa fa-check-circle text-success'
        ], $cm);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $container = $cm->getContainer();
        $em = $container->get('managers')->getManager();

                        $allRequests = array();
						$em->getConnection()->beginTransaction();
						try{
							foreach($ids as $id){
								$result = $em->getRepository($cm->getEntityName())
											->findOneById($id);
								if($result->hasStatus()){
									continue;
								}
								$result->setStatus($result::STATUS_ACTIVE);
								$em->persist($result);
								$allRequests[] = $result;
							}
							$em->flush();
							$em->getConnection()->commit();

							$container->get('session')->getFlashBag()->set('success', 'Success');
						}
						catch(\Exception $e){
							$em->close();
							$em->getConnection()->rollback();
							$container->get('session')->getFlashBag()->set('error', '<b>Fail</b>');
							return;
						}
						foreach($allRequests as $r){
							$container->get('rgs.mailer')->sendRequestConfirm($r);
						}
    }
}