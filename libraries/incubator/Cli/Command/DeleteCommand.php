<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Command;

use Joomla\Cli\Command;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\String\Inflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * The Delete command deletes entities.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class DeleteCommand extends Command
{
	/**
	 * Configure the options for the version command
	 *
	 * @return  void
	 */
	protected function configure()
	{
		$this
			->setName('delete')
			->setDescription('Delete entities')
			->addArgument(
				'entity',
				InputArgument::REQUIRED,
				'The name of the entity to delete.'
			)
			->addOption(
				'filter',
				'f',
				InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Select items using the FILTER condition.'
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

		$entity = ucfirst(Inflector::getInstance()->toSingular($input->getArgument('entity')));

		$repositoryFactory  = $this->container->get('Repository');
		$repository         = $repositoryFactory->forEntity($entity);
		$finder             = $repository->findAll();
		$idAccessorRegistry = $repositoryFactory->getIdAccessorRegistry();

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

		$count = 0;
		$force = $input->getOption('no-interaction');

		foreach ($records as $record)
		{
			$id     = $idAccessorRegistry->getEntityId($record);
			$choice = 'no';

			if (!$force)
			{
				$question = new Question(
					"Delete $entity #$id (yes,No,all)? ",
					'no'
				);
				$question->setAutocompleterValues(['yes', 'no', 'all']);

				$choice = $this->ask($input, $output, $question);

				if ($choice == 'all')
				{
					$force = true;
				}
			}

			if ($force || $choice == 'yes')
			{
				$repository->remove($record);
				$count++;
			}
		}

		$repository->commit();
		$this->writeln($output, "Deleted $count $entity item(s).");

		return 0;
	}
}
