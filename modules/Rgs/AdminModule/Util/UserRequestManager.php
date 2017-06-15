<?php

namespace Rgs\AdminModule\Util;

use Novice\Form\Field;
use Rgs\CatalogModule\Entity\Model\StatusInterface;

use Rgs\AdminModule\Util\ContentManager\Tools as CMTools;
use Utils\ToolFieldsUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Novice\Module\ContentManagerModule\Util\ContentManagerInterface,
    Novice\Module\ContentManagerModule\Util\ToolButtonsGroup;

use Novice\Module\SmartyBootstrapModule\Util\ItemProperty;

class UserRequestManager implements ContentManagerInterface {

    protected $container;
    protected $fieldCreator;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
        $this->fieldCreator = new ToolFieldsUtils();
    }

    public function getContainer(){
        return $this->container;
    }

    public function getName(){
        return '';
    }

    // must return an array of \Novice\Form\Field\Field
    public function getCustomFields(){
        return [
            'visibility' => $this->fieldCreator->createVisibilityField(array(
				'all' => "All",
				StatusInterface::STATUS_ACTIVE => "Accepted",
				StatusInterface::STATUS_ARCHIVED => "Declined",
                'null' => "Not Treated"
			))
        ];
    }

    // return the new array 'where'. if not, the 'where' will stay as it was before the method call
    public function processCustomFields($request, array $where, $customFields){
        $visibility = 'all';
        if($request->request->has('visibility')){
			$visibility = $request->request->get('visibility');
		}
        if($visibility != 'all'){
            if($visibility == 'null'){
                $where['i.status'] = null;
            }
            else{
                $where['i.status'] = $visibility;
            }
		}
        $customFields['visibility']->setValue($visibility);
        return $where;
    }

    public function getToolButtonsGroup(){
        return new ToolButtonsGroup([
            new CMTools\EditButton($this),
            new CMTools\UserRequestValidButton($this),
            new CMTools\UserRequestRefuseButton($this),
            new CMTools\DeleteButton($this)
        ]);
    }
    

    public function getTitle(){
        return 'layout.user_requests';
    }

    public function getDefaultOrder(){
      return  'i.createdAt DESC';
    }

    public function getOrderOptions(){
        return array( 
				"i.createdAt DESC" => "Newest",
				"i.createdAt ASC" => "Oldest"
			);
    }

    public function getVisibilityKey(){
        return 'i.status';
    }

    public function getEntityName(){
        return 'RgsCatalogModule:UserRequest';
    }

    public function getAlias(){
        return 'i';
    }

    public function getEditRouteId(){
        return 'rgs_admin_requests_edit';
    }

    public function getColumns(){
        return array(
            [
				'property' => 'status',
				'label' => 'Status',
				'filter' => function($propertyValue, $entity, $i, $smarty){
					if(!$entity->hasStatus()){
						return;
					}
                    else if($entity->isActive()){
                        $src = $smarty->getAssets()->getUrl(AdminModuleUtils::ASSET_OK, null);
                    }
					else{
						$src = $smarty->getAssets()->getUrl(AdminModuleUtils::ASSET_CANCEL, null);
					}

					$result = '';
					$result .= '<input type="image"';
					$result .= ' src="'.$src.'"';
					$result .= ' class="btn btn-outline btn-default"';
					$result .= ' />';

					return $result;
				}
			],
            [
                'property' => 'subject',
                'label' => 'Subject',
                'route' => [
					'id' => 'rgs_admin_requests_edit',
					'params' =>[
						'id' => new ItemProperty('id')
					],
					'absolute' => true
				]
            ],
            [
                'property' => 'user',
                'label' => 'From',
                'filter' => function($propertyValue, $entity, $i, $smarty){
                    $router = $smarty->getContainer()->get('router');
                    $link = $router->generate('rgs_admin_users_edit', ['id' => $entity['user']['id']]);
					return '<a href="'.$link.'" target="_blank">'.$entity['user']['login'].'</a> ('.$entity['user']['email'].')';;
				}
            ],
            [
                'property' => 'createdAt',
                'label' => 'Created at',
                'filter' => function($propertyValue){
                    if(!($propertyValue instanceof \DateTime)){
                        return;
                    }
                    return $propertyValue->format("Y-m-d H:i:s");
                }
            ]
        );
    }
}