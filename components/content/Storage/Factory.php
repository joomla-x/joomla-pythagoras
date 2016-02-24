<?php

namespace Joomla\Component\Content\Storage;

use Joomla\ORM\Storage\StorageProviderInterface;

class Factory implements StorageProviderInterface
{
	public function getEntityFinder($entityName)
	{
		return new Model(Model::ENTITY);
	}

	public function getCollectionFinder($entityName)
	{
		return new Model(Model::COLLECTION);
	}

	public function getPersistor($entityName)
	{
		return new Model(Model::COLLECTION);
	}
}
