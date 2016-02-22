<?php

namespace Joomla\Component\Content\Storage;

use Joomla\ORM\Entity\EntityInterface;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;

class Model implements EntityFinderInterface, CollectionFinderInterface, PersistorInterface
{
	private $conditions = [];

	public function orderBy($column, $direction = 'ASC')
	{
		return $this;
	}

	public function columns($columns)
	{
		return $this;
	}

	public function with($lValue, $op, $rValue)
	{
		$this->conditions[] = "{$lValue} {$op} '{$rValue}'";

		return $this;
	}

	public function get($count = 0, $start = 0)
	{
		return print_r($this->conditions, true);
	}

	public function store(EntityInterface $entity)
	{
		echo "Storing {$entity->type()}#{$entity->id}\n";
	}

	public function delete(EntityInterface $entity)
	{
		echo "Deleting {$entity->type()}#{$entity->id}\n";
	}
}
