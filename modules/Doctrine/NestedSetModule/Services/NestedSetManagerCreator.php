<?php
namespace Doctrine\NestedSetModule\Services;

use DoctrineModule\ManagerRegistry;
use DoctrineExtensions\NestedSet as NestedSet;

class NestedSetManagerCreator
{
	protected $doctrine;
	
	private $leftFieldName = 'lft';
	private $rightFieldName = 'rgt';
	private $rootFieldName = 'root';

	public function __construct(ManagerRegistry $doctrine, Array $fieldNames = array())
	{
		$this->doctrine = $doctrine;
		
		if(!empty($fieldNames))
		{
			foreach ($fieldNames as $type => $value)
			{
				$method = 'set'.ucfirst($type);
      
				if (is_callable(array($this, $method)))
				{
					$this->$method($value);
				}
			}
		}
	}

	public function setLeftFieldName($leftFieldName)
	{
		$this->leftFieldName = $leftFieldName;
		return $this;
	}

	public function setRightFieldName($rightFieldName)
	{
		$this->rightFieldName = $rightFieldName;
		return $this;
	}

	public function setRootFieldName($rootFieldName)
	{
		$this->rootFieldName = $rootFieldName;
		return $this;
	}

	public function getLeftFieldName()
	{
		return $this->leftFieldName;
	}

	public function getRightFieldName()
	{
		return $this->rightFieldName;
	}

	public function getRootFieldName()
	{
		return $this->rootFieldName;
	}


	public function getManager($clazz, $managerName='')
	{
		$config = new NestedSet\Config($this->doctrine->getManager($managerName), $clazz);
		
		$config->setLeftFieldName($this->getLeftFieldName());

		$config->setRightFieldName($this->getRightFieldName());

		$config->setRootFieldName($this->getRootFieldName());
		
		return new NestedSet\Manager($config);
	}
}
