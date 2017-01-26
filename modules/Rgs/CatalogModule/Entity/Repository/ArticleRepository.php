<?php

namespace Rgs\CatalogModule\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Rgs\CatalogModule\Entity\Article;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
	public function publish(array $ids, $publish = Article::PUBLISHED){
		foreach($ids as $id){
			$this->publishOneArticle($id, $publish);
		}
	}

	public function publishOneArticle($id, $publish = Article::PUBLISHED){
		$qb = $this->createQueryBuilder('a');

		return $qb->update('RgsCatalogModule:Article', 'a')
			->set('a.published', ':published')
			->where('a.id = :id')
			->setParameter('published', $publish)
			->setParameter('id', $id)
			->getQuery()
			->execute();
	}

	public function deleteOneById($id)
	{
		$qb = $this->createQueryBuilder('a');

		return $qb->delete('RgsCatalogModule:Article', 'a')
			->where('a.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->execute();
	}

	public function deleteByIds(array $ids)
	{
		foreach($ids as $id){
			$this->deleteOneById($ids);
		}
	}
	
	public function findByIds(array $ids)
	{
		if(count($ids) == 0){
			return array();
		}
		$qb = $this->createQueryBuilder('a');
		
		$i = 1;
		foreach($ids as $id){
			$qb->orWhere($qb->expr()->eq('a.id', '?'.$i))
				->setParameter($i, $id);
			//$qb	->addOrderBy('a.id', 'ASC');
			$i++;
		}

		return $qb->getQuery()->getResult();
		
	}

	public function getArticles($limit = 20, $page = 1, $orderBy = 'a.name', $ascending = 'ASC', $published = null)
	{
		$qb = $this->createQueryBuilder('a');
		
		if($published !== null){
			$qb->andWhere($qb->expr()->eq('a.published', ':published'))
				->setParameter('published', (bool)$published);
		}

		$qb	->orderBy($orderBy, $ascending)
			->setFirstResult(($page-1) * $limit)
			->setMaxResults($limit);

		return new Paginator($qb);
	}

	public function findArticles($limit = 20, $page = 1, $where = array(), $orderBy = array())
	{
		$qb = $this->createQueryBuilder('a');
		
		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		if(empty($orderBy))
			$orderBy = array('a.name' => 'ASC');

		foreach($orderBy as $k => $v){
			$qb	->addOrderBy($k, $v);
		}
		
		$qb ->setFirstResult(($page-1) * $limit)
			->setMaxResults($limit);

		return new Paginator($qb);
	}

	public function countArticles($where = array())
	{
		$qb = $this->createQueryBuilder('a');

		$qb->select('count(a.id)');

		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		return $qb->getQuery()->getSingleScalarResult();
	}
	
	
	// BELOW, ALL THE METHODS FOR FRONTEND
	
	private function getFrontArticlesQueryBuilder(){
		
		$qb = $this->createQueryBuilder('a');
		
		// On fait une jointure avec l'entité Categorie avec pour alias « c »
    	$qb
      		->join('a.categorie', 'c')
      		->addSelect('c');
		
		// On fait une jointure avec l'entité Etat avec pour alias « e »	
		$qb
      		->join('a.etat', 'e')
      		->addSelect('e');
			
		$qb->andWhere($qb->expr()->eq('a.published', ':article_published'))
				->setParameter('article_published', true)
				->andWhere($qb->expr()->eq('c.published', ':category_published'))
				->setParameter('category_published', true)
				->andWhere($qb->expr()->eq('e.published', ':etat_published'))
				->setParameter('etat_published', true);
		
		return $qb;
	}
	
	public function getFrontArticles($limit = 20, $page = 1, $where = array(), $orderBy = array())
	{		
		$qb = $this->getFrontArticlesQueryBuilder();
		
		/*dump($qb->getAllAliases());
		exit(__METHOD__);*/
		
		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		if(empty($orderBy))
			$orderBy = array('a.name' => 'ASC');

		foreach($orderBy as $k => $v){
			$qb	->addOrderBy($k, $v);
		}
		
		$paginator = new Paginator($qb);
		
		$totalItems = count($paginator);
		$pagesCount = ceil($totalItems / $limit);
		if($page > $pagesCount)
			$page = $pagesCount;
		if($page == 0)
			$page = 1;
		
		$paginator->getQuery()
					->setFirstResult(($page-1) * $limit)
					->setMaxResults($limit);

		return $paginator;
	}
	
	public function countFrontArticles()
	{		
		$qb = $this->getFrontArticlesQueryBuilder();
		
		$qb->select('count(a.id)');
		
		return $qb->getQuery()->getSingleScalarResult();
	}
}
