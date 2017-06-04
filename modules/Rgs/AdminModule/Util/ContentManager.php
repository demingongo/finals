<?php

namespace Rgs\AdminModule\Util;

use Novice\Form\Field;
use Rgs\CatalogModule\Entity\Model\PublishedInterface;

use Rgs\AdminModule\Util\ContentManager\Tools as CMTools;
use Utils\ToolFieldsUtils;

abstract class ContentManager 
{

    protected $container;
    protected $fieldCreator;

    public function __construct($container){
        $this->container = $container;
        $this->fieldCreator = new ToolFieldsUtils();
    }

    public function getName(){
        return '';
    }

    public function getTitle(){
        return '';
    }

    // must return an array of \Novice\Form\Field\Field
    public function getCustomFields(){
        return [
            'visibility' => $this->fieldCreator->createVisibilityField(array(
				'all' => "All",
				PublishedInterface::PUBLISHED => "published",
				PublishedInterface::NOT_PUBLISHED => "not published",
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
			$where[$this->getVisibilityKey()] = (bool) $visibility;
		}
        $customFields['visibility']->setValue($visibility);
        return $where;
    }

    public function getColumns(){
        return array();
    }

    public function getToolsButtons(){
        return [
            new CMTools\AddButton(),
            new CMTools\EditButton(),
            new CMTools\PublishButton(),
            new CMTools\UnpublishButton(),
            new CMTools\DeleteButton()
        ];
    }

    abstract public function getDefaultOrder();

    abstract public function getOrderOptions();

    abstract public function getVisibilityKey();

    abstract public function getEntityName();

    abstract public function getAlias();

    abstract public function getAddRouteId();

    abstract public function getEditRouteId();
}