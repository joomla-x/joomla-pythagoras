<?php

namespace Joomla\ORM\Storage;

class CsvProvider implements StorageProviderInterface
{
	private $dataFile;

	public function __construct($dataFile)
	{
		$this->dataFile = $dataFile;
	}

	public function getEntityFinder($entityName)
	{
		return new CsvModel($this->dataFile, CsvModel::ENTITY);
	}

	public function getCollectionFinder($entityName)
	{
		return new CsvModel($this->dataFile, CsvModel::COLLECTION);
	}

	public function getPersistor($entityName)
	{
		return new CsvModel($this->dataFile, CsvModel::COLLECTION);
	}
}
