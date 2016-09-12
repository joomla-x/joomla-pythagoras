<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Commands;

use Joomla\Cli\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The version command reports the version of a Joomla! installation.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class VersionCommand extends Command
{
	/**
	 * Configure the options for the version command
	 *
	 * @return  void
	 */
	protected function configure()
	{
		$this
			->setName('version')

			->setDescription('Show the Joomla! version')

			->addOption(
				'long',
				'l',
				InputOption::VALUE_NONE,
				'The long version info, eg. Joomla! x.y.z Stable [ Codename ] DD-Month-YYYY HH:ii GMT (default).'
			)

			->addOption(
				'short',
				's',
				InputOption::VALUE_NONE,
				'The short version info, eg. x.y.z'
			)

			->addOption(
				'release',
				'r',
				InputOption::VALUE_NONE,
				'The release info, eg. x.y'
			)
		;
	}

	/**
	 * Execute the version command
	 *
	 * @param   InputInterface   $input   An InputInterface instance
	 * @param   OutputInterface  $output  An OutputInterface instance
	 *
	 * @return  void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setupEnvironment($input, $output);

		if ($input->getOption('short'))
		{
			$result = 'X.0.0';
		}
		elseif ($input->getOption('release'))
		{
			$result = 'X.0';
		}
		else
		{
			$result = 'Joomla! X.0.0 Dev [ Pythagoras ].';
		}
		$output->writeln($result);
	}
}
