<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Command;

use Joomla\Cli\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The show command shows a list of entities.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class ShowCommand extends Command
{
	/**
	 * Configure the options for the version command
	 *
	 * @return  void
	 */
	protected function configure()
	{
		$this
			->setName('show')
			->setDescription('Show a list of entities')
			->addArgument(
				'entity',
				InputArgument::REQUIRED,
				'The name of the entity to retrieve.'
			)
			->addOption(
				'label',
				'l',
				InputOption::VALUE_NONE,
				'Use labels as column headers instead of column names.'
			)
			->addOption(
				'filter',
				'f',
				InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Filter the list using the FILTER condition.'
			)
			->addOption(
				'compact',
				null,
				InputOption::VALUE_NONE,
				'Output a compact table.'
			);
	}

	/**
	 * Execute the version command
	 *
	 * @param   InputInterface  $input  An InputInterface instance
	 * @param   OutputInterface $output An OutputInterface instance
	 *
	 * @return  integer  0 if everything went fine, 1 on error
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setupEnvironment($input, $output);

		$entity = $this->normaliseEntityName($input->getArgument('entity'));

		$repositoryFactory = $this->container->get('Repository');
		$repository        = $repositoryFactory->forEntity($entity);
		$finder            = $repository->findAll();

		foreach ($input->getOption('filter') as $filter)
		{
			if (!preg_match('~^(\w+)(\s*\W+\s*|\s+\w+\s+)(\w+)$~', trim($filter), $match))
			{
				$this->writeln($output, "Cannot interpret filter $filter");

				return 1;
			}

			$finder = $finder->with($match[1], trim($match[2]), $match[3]);
		}

		$records = $finder->getItems();

		if (empty($records))
		{
			$this->writeln($output, "No matching records found");

			return 0;
		}

		$table = new Table($output);

		if ($input->getOption('compact'))
		{
			$table->setStyle('compact');
		}
		else
		{
			$table->setStyle('default');
		}

		$entityBuilder = $repositoryFactory->getEntityBuilder();
		$meta          = $entityBuilder->getMeta($entity);
		$fields        = array_merge($meta->fields, $meta->relations['belongsTo']);
		$headers       = [];
		$useLabel      = $input->getOption('label');

		foreach ($fields as $field)
		{
			$headers[] = $useLabel ? $field->label : $field->name;
		}

		$table->setHeaders($headers);

		foreach ($records as $record)
		{
			$table->addRow($entityBuilder->reduce($record));
		}

		$table->render();

		return 0;
	}
}
