<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The main Joomla CLI application.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class Application extends BaseApplication
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('Joomla CLI', '__DEPLOY_VERSION__');
		$this->setCatchExceptions(false);

		$this->addPlugins(__DIR__ . '/Commands');
	}

	/**
	 * Runs the current application.
	 *
	 * @param   InputInterface   $input   An InputInterface instance
	 * @param   OutputInterface  $output  An OutputInterface instance
	 *
	 * @return  integer  0 if everything went fine, or an error code
	 *
	 * @throws  \Exception on problems
	 */
	public function run(InputInterface $input = null, OutputInterface $output = null)
	{
		try
		{
			parent::run($input, $output);
		}
		catch (\Exception $e)
		{
			if (null === $output) {
				$output = new ConsoleOutput();
			}
			$message = [
				$this->getLongVersion(),
				'',
				$e->getMessage(),
				''
			];
			$output->writeln($message);
		}
	}

	/**
	 * Dynamically add all commands from a path
	 *
	 * @param   string  $path  The directory with the plugins
	 *
	 * @return  void
	 */
	private function addPlugins($path)
	{
		foreach (glob($path . '/*.php') as $filename)
		{
			$commandClass = __NAMESPACE__ . '\\Commands\\' . basename($filename, '.php');
			$command = new $commandClass;
			$this->add($command);
		}
	}
}
