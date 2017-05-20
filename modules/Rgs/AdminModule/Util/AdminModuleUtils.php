<?php

namespace Rgs\AdminModule\Util;

use Novice\Module\SmartyBootstrapModule\Util\ItemProperty;

class AdminModuleUtils {

    const ASSET_OK = '/img/pictos/Ok-16.png';

    const ASSET_CANCEL = '/img/pictos/Cancel_2-16.png';

    private function getPublishedColumn(){
        return [
				'property' => 'published',
				'label' => 'Status',
				'filter' => function($propertyValue, $entity, $i, $smarty){
					if($entity->isPublished()){
						$publishValue='unpublish';
						$src = $smarty->getAssets()->getUrl(self::ASSET_OK, null);
					}
					else{
						$publishValue='publish';
						$src = $smarty->getAssets()->getUrl(self::ASSET_CANCEL, null);
					}

					$result = '';
					$result .= '<input type="image"';
					$result .= ' src="'.$src.'"';
					$result .= ' class="btn btn-outline btn-default" name="submit[]"';
					$result .= ' data-check-id="cb'.$i.'"';
					$result .= ' value="'.$publishValue.'" />';

					return $result;
				}
			];
    }

    public function getArticlesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_articles_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			],
			[
				'property' => 'categorie.name',
				'label' => 'Category',
				'class' => 'hidden-xs',
				'route' => [
					'id' => 'rgs_admin_categories_edit',
					'params' =>[
						'id' => new ItemProperty('categorie.id'), 
						'slug' => new ItemProperty('categorie.slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getCategoriesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_categories_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getStatesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_etats_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getBrandsColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_marques_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

}