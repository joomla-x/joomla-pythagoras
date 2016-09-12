<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Commands;

use Joomla\Cli\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Joomla\Cms\Installer\Installer;

/**
 * The Install command allows the installation of Joomla! extensions from the command line.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class InstallCommand extends Command
{
	/**
	 * Configure the options for the install command
	 *
	 * @return  void
	 */
	protected function configure()
	{
		$this
			->setName('install')

			->setDescription('Install a Joomla! extension')

			->addArgument(
			'extension',
				InputArgument::REQUIRED | InputArgument::IS_ARRAY,
				'The path to the extension.'
			)
		;
	}

	/**
	 * Execute the install command
	 *
	 * @param   InputInterface  $input  An InputInterface instance
	 * @param   OutputInterface $output An OutputInterface instance
	 *
	 * @return  integer  0 if everything went fine, 1 on error
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setupEnvironment($input, $output);

		try
		{
			$arguments = $input->getArguments();

			$installer = new Installer(JPATH_ROOT . "/data");

			$count = 0;

			foreach ($arguments['extension'] as $extension)
			{
				$source = realpath($extension);

				if (!file_exists($source))
				{
					$this->writeln($output, "Unable to locate $extension");

					continue;
				}

				$installer->install($source);
				$count++;
			}

			$installer->finish();

			$this->writeln($output, "Installed $count extension(s)");

			return 0;
		}
		catch (\Exception $e)
		{
			$this->writeln($output, $e->getMessage());

			return 1;
		}
	}
}
