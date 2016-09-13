<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli;

use Joomla\Cli\Exception\InvalidFilterException;
use Joomla\Cli\Exception\NoRecordsException;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\String\Inflector;
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
		}
		catch (InvalidFilterException $e)
		{
			$this->writeln($output, $e->getMessage());

			return 1;
		}
		catch (NoRecordsException $e)
		{
			$this->writeln($output, $e->getMessage());

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

		if (!$inflector->isSingular($entity))
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
}
