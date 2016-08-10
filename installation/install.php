<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;
use Joomla\DI\Container;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Service\StorageServiceProvider;

define('JPATH_ROOT', realpath(dirname(__DIR__)));

ini_set('date.timezone', 'UTC');

require_once JPATH_ROOT . '/libraries/vendor/autoload.php';

$installer = new Installer(JPATH_ROOT . "/data");
$installer->install(JPATH_ROOT . '/libraries/incubator/PageBuilder');

class Installer
{
	/** @var EntityBuilder */
	private $builder;

	/** @var Container */
	private $container;

	/** @var  string */
	private $dest;

	/** @var  RepositoryFactory */
	private $repositoryFactory;

	public function __construct($dest)
	{
		$this->container = new Container();
		$this->dest      = $dest;

		$storage = new StorageServiceProvider;
		$storage->register($this->container);

		$this->repositoryFactory = $this->container->get('Repository');
		$this->builder           = $this->repositoryFactory->getEntityBuilder();
	}

	public function install($source)
	{
		$config = parse_ini_file($source . '/Entity/database.ini', true);

		foreach ($config as $class => $settings)
		{
			$this->builder->add($class, $settings);
			$this->process($class, $settings, $source);
		}
	}

	private function process($class, $settings, $source)
	{
		$destination = $this->dest . '/entities/' . $settings['definition'];

		if (file_exists($destination))
		{
			unlink($destination);
		}

		copy($source . '/Entity/' . $settings['definition'], $destination);

		switch ($settings['dataMapper'])
		{
			case \Joomla\ORM\Storage\Csv\CsvDataMapper::class:
				$dataFile = $settings['table'] . '.csv';

				if (file_exists($source . '/data/' . $dataFile))
				{
					copy($source . '/data/' . $dataFile, $this->dest . '/' . $dataFile);
				}

				break;

			case \Joomla\ORM\Storage\Doctrine\DoctrineDataMapper::class:
				$meta  = $this->builder->getMeta($class);
				$table = new Table($settings['table']);

				foreach ($meta->fields as $field)
				{
					$type = 'string';

					if (in_array($field->type, ['int', 'integer', 'id']))
					{
						$type = 'integer';
					}

					$table->addColumn($field->columnName($field->name), $type);
				}

				$primary = explode(',', $meta->primary);
				$table->setPrimaryKey($primary);

				$connection = $this->repositoryFactory->getConnection(Connection::class);
				$connection->getSchemaManager()->createTable($table);

				$dataFile = $source . '/data/' . $settings['table'] . '.csv';

				if (file_exists($dataFile))
				{
					foreach ($this->loadData($dataFile) as $row)
					{
						$connection->insert($table, $row);
					}
				}

				break;
		}
	}

	/**
	 * Load the data from the file
	 */
	private function loadData($dataFile)
	{
		$fh   = fopen($dataFile, 'r');
		$keys = fgetcsv($fh);

		$rows = [];

		while (!feof($fh))
		{
			$row = fgetcsv($fh);

			if ($row === false)
			{
				break;
			}

			$rows[] = array_combine($keys, $row);
		}

		fclose($fh);

		return $rows;
	}
}
