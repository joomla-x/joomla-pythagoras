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
use Doctrine\DBAL\Types\Type;
use Joomla\Cli\Exception\InvalidFilterException;
use Joomla\Cli\Exception\NoRecordsException;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\String\Inflector;
use Symfony\Component\Console\Helper\Table;
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

	protected function dumpSql(InputInterface $input, OutputInterface $output)
	{
		$connection = $this->repositoryFactory->getConnection(Connection::class);

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

		$table = new Table($output);

		if ($input->getOption('compact'))
		{
			$table->setStyle('compact');
		}
		else
		{
			$table->setStyle('default');
		}

		$table->setHeaders(['#', 'SQL', 'Time']);

		foreach ($queries as $index => $query)
		{
			$sql     = $query['sql'];
			$params  = $query['params'];
			$types   = $query['types'];
			$pointer = 0;
			$sql     = preg_replace_callback(
				'~\?~',
				function () use ($params, $types, $pointer)
				{
					$value = $params[$pointer];
					if (isset($types[$pointer]))
					{
						throw new \Exception(__METHOD__ . ": Encountered uncovered type {$types[$pointer]}");
					}

					return $value;
				},
				$sql
			);
			$sql = preg_replace('~(WHERE|LIMIT)~', "\n  \\1", $sql);
			$table->addRow([$index, "$sql\n", 1000 * $query['executionMS']]);
		}

		$table->render();
	}

	/**
	 * Binds a set of parameters, some or all of which are typed with a PDO binding type
	 * or DBAL mapping type, to a given statement.
	 *
	 * @param Connection                      $connection
	 * @param \Doctrine\DBAL\Driver\Statement $stmt   The statement to bind the values to.
	 * @param array                           $params The map/list of named/positional parameters.
	 * @param array                           $types  The parameter types (PDO binding types or DBAL mapping types).
	 *
	 * @return void
	 *
	 * @internal Duck-typing used on the $stmt parameter to support driver statements as well as
	 *           raw PDOStatement instances.
	 */
	private function bindTypedValues($connection, $stmt, array $params, array $types)
	{
		// Check whether parameters are positional or named. Mixing is not allowed, just like in PDO.
		if (is_int(key($params)))
		{
			// Positional parameters
			$typeOffset = array_key_exists(0, $types) ? -1 : 0;
			$bindIndex  = 1;
			foreach ($params as $value)
			{
				$typeIndex = $bindIndex + $typeOffset;
				if (isset($types[$typeIndex]))
				{
					$type = $types[$typeIndex];
					list($value, $bindingType) = $this->getBindingInfo($connection, $value, $type);
					$stmt->bindValue($bindIndex, $value, $bindingType);
				}
				else
				{
					$stmt->bindValue($bindIndex, $value);
				}
				++$bindIndex;
			}
		}
		else
		{
			// Named parameters
			foreach ($params as $name => $value)
			{
				if (isset($types[$name]))
				{
					$type = $types[$name];
					list($value, $bindingType) = $this->getBindingInfo($connection, $value, $type);
					$stmt->bindValue($name, $value, $bindingType);
				}
				else
				{
					$stmt->bindValue($name, $value);
				}
			}
		}
	}

	/**
	 * Gets the binding type of a given type. The given type can be a PDO or DBAL mapping type.
	 *
	 * @param Connection $connection
	 * @param mixed      $value The value to bind.
	 * @param mixed      $type  The type to bind (PDO or DBAL).
	 *
	 * @return array [0] => the (escaped) value, [1] => the binding type.
	 */
	private function getBindingInfo($connection, $value, $type)
	{
		if (is_string($type))
		{
			$type = Type::getType($type);
		}
		if ($type instanceof Type)
		{
			$value       = $type->convertToDatabaseValue($value, $connection->getDatabasePlatform());
			$bindingType = $type->getBindingType();
		}
		else
		{
			$bindingType = $type; // PDO::PARAM_* constants
		}

		return [$value, $bindingType];
	}
}
