<?php

namespace Joomla\Component\Content\Storage;

use Joomla\ORM\Repository\StorageProviderInterface;

class Factory implements StorageProviderInterface
{
	public function getEntityFinder($entityName)
	{
		return new Model;
	}

	public function getCollectionFinder($entityName)
	{
		return new Model;
	}

	public function getPersistor($entityName)
	{
		return new Model;
	}
}
