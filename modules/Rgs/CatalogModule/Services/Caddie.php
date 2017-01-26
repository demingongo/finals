<?php
namespace Rgs\CatalogModule\Services;

use Rgs\CatalogModule\Entity\Article;
use Rgs\CatalogModule\Entity\Reservation;
use Rgs\CatalogModule\Entity\ReservationArticle;
use Symfony\Component\HttpFoundation\Session\Session;

class Caddie
{	

	private $name = 'app/user/caddie';
	
	private $doctrine;

	private $session;
	
	//private $reservation;
	
	//private $articles = array();
	//private $updated;
	
	private $reservationArticles = array();
	
	private $minStock;
	
	private $qteTotal;

	public function __construct(Session $session, $doctrine, $minStock = 0)
	{
		$this->session = $session;
		$this->doctrine = $doctrine;
		$this->minStock = $minStock;
		$this->qteTotal = 0;
		//$this->updated = true;
		
		if(!$this->session->has($this->name) || ($this->session->has($this->name) && !is_array($this->session->get($this->name)))){
			$this->session->set($this->name, array());
		}
	}
	
	private function updateSession(Array $array){
		$this->session->set($this->name, $array);
		//$this->updated = true;
	}
	
	private function findOneArticleById($id){
		return $this->doctrine->getManager()->getRepository('RgsCatalogModule:Article')->findOneById($id);
	}
	
	public function update(){
		$this->qteTotal = 0;
		$this->reservationArticles = array();
		$array = $this->session->get($this->name);
		
		$idsToUnset = array();
		
		/*$articles = $repository->findByIds(array_keys($array));
		foreach($articles as $article){
			dump($array);
			dump($articles);
			exit(__METHOD__);
			if($article->getStock() > $this->minStock){
				$this->setQuantityP($article, $qte);
			}
			else{
				$idsToUnset[] = $id;
			}
		}*/
		
		foreach($array as $id => $qte){
			$article = $this->findOneArticleById($id);
			if($article == null){
				$idsToUnset[] = $id;
				continue;
			}
			if($article->getStock() > $this->minStock){
				$this->setQuantityP($article, $qte);
			}
			else{
				$idsToUnset[] = $id;
			}
		}
		
		foreach($idsToUnset as $id){
			unset($array[$id]);
		}
		
		$this->updateSession($array);
		
	}
	
	private function setQuantityP(Article $article, $qte){
			
			if($qte <= 0){
				return;
			}
			
			$array = $this->session->get($this->name);
			$id = $article->getId();
			
			if(isset($this->reservationArticles[$id])){
				$this->reservationArticles[$id]->setQuantite($qte);
				$array[$id] = $qte;
			}
			else{		
				$r = new ReservationArticle();
				$r->setArticle($article);
				$r->setQuantite($qte);
				$r->setPrixUnitaire($article->getPrix());
			
				$this->reservationArticles[$id] = $r;
				$array[$id] = $qte;
			}
			$this->qteTotal += $qte;
			/*if($qte <= 0){
				return;
			}
			
			$array = $this->session->get($this->name);
			$id = $article->getId();
			
			if(isset($this->articles[$id])){
				$array[$id] = $qte;
			}
			else{		
				$this->articles[$id] = $article;
				$array[$id] = $qte;
			}*/
	}
	
	/**
	 * @return bool
	 */
	public function add(Article $article){
			
			$array = $this->session->get($this->name);
			$id = $article->getId();
			
			
			if(isset($this->reservationArticles[$id])){
				$newQte = $this->reservationArticles[$id]->getQuantite()+1;
				
				if($article->getStock() - $newQte < $this->minStock){
					return false;
				}
				
				$this->reservationArticles[$id]->setQuantite($newQte);
				$array[$id] = $newQte;
				$this->qteTotal += $newQte;
			}
			else{	
				if($article->getStock() - 1 < $this->minStock){
					return false;
				}
				$r = new ReservationArticle();
				$r->setArticle($article);
				$r->setQuantite(1);
				$r->setPrixUnitaire($article->getPrix());
			
				$this->reservationArticles[$id] = $r;
				$array[$id] = 1;
				$this->qteTotal += 1;
			}
			
			$this->updateSession($array);
			
			return true;
			
			/*$array = $this->session->get($this->name);
			$id = $article->getId();		
			
			if(isset($this->articles[$id])){
				$newQte = $array[$id]+1;
				
				if($article->getStock() - $newQte < $this->minStock){
					return false;
				}
				$array[$id] = $newQte;
			}
			else{	
				if($article->getStock() - 1 < $this->minStock){
					return false;
				}
				$this->articles[$id] = $article;
				$array[$id] = 1;
			}
			
			$this->updateSession($array);
			
			return true;*/
	}
	
	/**
	 * @return bool
	 */
	public function setQuantity($id, $qte){
			
			if($qte <= 0){
				return false;
			}
			
			$array = $this->session->get($this->name);
			
			if(isset($this->reservationArticles[$id])){
				if($this->reservationArticles[$id]->getArticle()->getStock() - $qte < $this->minStock){
					return false;
				}
				$this->reservationArticles[$id]->setQuantite($qte);
				$array[$id] = $qte;
			}
			else{
				$article = $this->findOneArticleById($id);
				if($article == null || ($article->getStock() - $qte < $this->minStock)){
					return false;
				}		
				$r = new ReservationArticle();
				$r->setArticle($article);
				$r->setQuantite($qte);
				$r->setPrixUnitaire($article->getPrix());
			
				$this->reservationArticles[$id] = $r;
				$array[$id] = $qte;
			}
			
			$this->qteTotal += $qte;
			$this->updateSession($array);
			
			return true;
			
			/*if($qte <= 0){
				return false;
			}
			
			if($article->getStock() - $qte < $this->minStock){
				return false;
			}		
			
			$array = $this->session->get($this->name);
			$id = $article->getId();
			
			if(isset($this->articles[$id])){
				$array[$id] = $qte;
			}
			else{
				$this->articles[$id] = $article;
				$array[$id] = $qte;
			}
			
			$this->updateSession($array);
			
			return true;*/
	}
	
	/**
	 * @return true
	 */
	public function remove($id){
			
			$array = $this->session->get($this->name);
			
			if(isset($this->reservationArticles[$id])){
				$this->qteTotal -= $this->reservationArticles[$id]->getQuantite();
				unset($this->reservationArticles[$id]);
			}
			if(isset($array[$id])){		
				unset($array[$id]);
			}
			
			$this->updateSession($array);
			
			return true;
			
			/*$array = $this->session->get($this->name);
			$id = $article->getId();
			
			if(isset($this->articles[$id])){
				unset($this->articles[$id]);
			}
			if(isset($array[$id])){		
				unset($array[$id]);
			}
			
			$this->updateSession($array);
			
			return true;*/
	}
	
	/**
	 * @return bool
	 */
	public function has(Article $article){
			
			$array = $this->session->get($this->name);
			$id = $article->getId();
			
			return (isset($this->reservationArticles[$id]) && isset($array[$id]));
	}
	
	public function removeAll(){
		$this->qteTotal = 0;
		$this->reservationArticles = array();
		$this->updateSession(array());
	}
	
	/**
	 * @return ReservationArticle[]
	 */
	public function findAll(){
			/*if($this->updated){
				$this->updated = false;
			}*/
			return $this->reservationArticles;
	}

	public function count(){
			//return count($this->findAll());
			return $this->qteTotal;
	}
}
