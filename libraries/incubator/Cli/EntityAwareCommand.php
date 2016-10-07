<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Joomla\Cli\Exception\InvalidFilterException;
use Joomla\Cli\Exception\NoRecordsException;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\String\Inflector;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The abstract entity aware command provides common methods for commands handling entities.
 *
 * It adds the 'entity' argument and a 'filter' option to the command.
 * Child classes must implement the 'doIt()' method (instead of 'execute()').
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
abstract class EntityAwareCommand extends Command
{
	/** @var RepositoryFactory */
	protected $repositoryFactory;

	/**
	 * Add options common to all commands
	 *
	 * @return  void
	 */
	protected function addGlobalOptions()
	{
		parent::addGlobalOptions();

		$this
			->addArgument(
				'entity',
				InputArgument::REQUIRED,
				'The name of the entity.'
			)
			->addOption(
				'filter',
				'f',
				InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Restrict the operation to items using the FILTER condition.'
			)
			->addOption(
				'dump-sql',
				'd',
				InputOption::VALUE_NONE,
				'Dump SQL queries'
			);
	}

	/**
	 * Execute the command
	 *
	 * @param   InputInterface  $input  An InputInterface instance
	 * @param   OutputInterface $output An OutputInterface instance
	 *
	 * @return  integer  0 if everything went fine, 1 on error
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setupEnvironment($input, $output);
		$this->repositoryFactory = $this->container->get('Repository');

		$entity     = $this->normaliseEntityName($input->getArgument('entity'));
		$repository = $this->repositoryFactory->forEntity($entity);
		$finder     = $repository->findAll();

		try
		{
			$this->applyFilter($input->getOption('filter'), $finder);
			$this->doIt($input, $output, $finder, $entity);
			$this->dumpSql($input, $output);
		}
		catch (InvalidFilterException $e)
		{
			$this->writeln($output, $e->getMessage());
			$this->dumpSql($input, $output);

			return 1;
		}
		catch (NoRecordsException $e)
		{
			$this->writeln($output, $e->getMessage());
			$this->dumpSql($input, $output);

			return 0;
		}

		return 0;
	}

	/**
	 * @param   InputInterface            $input  An InputInterface instance
	 * @param   OutputInterface           $output An OutputInterface instance
	 * @param   CollectionFinderInterface $finder The finder
	 * @param   string                    $entity The entity name
	 *
	 * @return  void
	 */
	abstract protected function doIt(InputInterface $input, OutputInterface $output, $finder, $entity);

	/**
	 * Normalises the entity name.
	 *
	 * @param   string $entity The entity name (singular or plural)
	 *
	 * @return  string The singular entity name
	 */
	protected function normaliseEntityName($entity)
	{
		$inflector = Inflector::getInstance();

		if ($inflector->isPlural($entity))
		{
			$entity = $inflector->toSingular($entity);
		}

		return ucfirst($entity);
	}

	/**
	 * Applies the filter conditions
	 *
	 * @param   string[]                  $conditions The filter options
	 * @param   CollectionFinderInterface $finder     The finder
	 *
	 * @return  CollectionFinderInterface
	 */
	protected function applyFilter($conditions, $finder)
	{
		foreach ($conditions as $filter)
		{
			if (!preg_match('~^(\w+)(\s*\W+\s*|\s+\w+\s+)(.+)$~', trim($filter), $match))
			{
				throw new InvalidFilterException("Cannot interpret filter $filter");
			}

			$finder = $finder->with($match[1], trim($match[2]), $match[3]);
		}

		return $finder;
	}

	/**
	 * Retrieves the selected records.
	 *
	 * @param   CollectionFinderInterface $finder The finder
	 *
	 * @return  object[]
	 */
	protected function getRecords($finder)
	{
		$records = $finder->getItems();

		if (empty($records))
		{
			throw new NoRecordsException("No matching records found");
		}

		return $records;
	}

	/**
	 * @param   InputInterface  $input  The input
	 * @param   OutputInterface $output The output
	 *
	 * @return  void
	 */
	protected function dumpSql(InputInterface $input, OutputInterface $output)
	{
		$connection = $this->repositoryFactory->getConnection();

		if (!$connection instanceof Connection)
		{
			$this->writeln($output, "Connection " . get_class_vars($connection) . " does not support SQL.");

			return;
		}

		$logger = $connection->getConfiguration()->getSQLLogger();

		if (!$logger instanceof DebugStack)
		{
			$this->writeln($output, "Debug logger is not enabled.");

			return;
		}

		$queries = $logger->queries;

		$table = $this->createTable($input, $output, ['#', 'SQL', 'Time']);

		foreach ($queries as $index => $query)
		{
			$sql    = $query['sql'];
			$params = $query['params'];

			ksort($params);

			$sql     = preg_replace_callback(
				'~\?~',
				function () use (&$params) {
					return array_shift($params);
				},
				$sql
			);
			$sql     = preg_replace('~(WHERE|LIMIT|INNER\s+JOIN|LEFT\s+JOIN)~', "\n  \\1", $sql);
			$sql     = preg_replace('~(AND|OR)~', "\n    \\1", $sql);
			$table->addRow([$index, "$sql\n", sprintf('%.3f ms', 1000 * $query['executionMS'])]);
		}

		$table->render();
	}
}
