<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli;

use Interop\Container\ContainerInterface;
use Joomla\String\Inflector;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * The abstract command provides common methods for most JoomlaCLI commands.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
abstract class Command extends BaseCommand
{
	/** @var  ContainerInterface */
	protected $container;

	/** @var  string */
	protected $basePath;

	/**
	 * Constructor.
	 *
	 * @param   string  $name  The name of the command
	 */
	public function __construct($name = null)
	{
		parent::__construct($name);

		$this->addGlobalOptions();
	}

	/**
	 * Sets a Dependancy Injection Container
	 *
	 * @param   ContainerInterface $container The container
	 *
	 * @return  void
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Add options common to all commands
	 *
	 * @return  void
	 */
	protected function addGlobalOptions()
	{
		$this
			->addOption(
				'basepath',
				'b',
				InputOption::VALUE_REQUIRED,
				'The root of the Joomla! installation. Defaults to the current working directory.',
				getcwd()
			);
	}

	/**
	 * Setup the environment
	 *
	 * @param   InputInterface   $input  An InputInterface instance
	 * @param   OutputInterface  $output An OutputInterface instance
	 *
	 * @return  void
	 */
	protected function setupEnvironment(InputInterface $input, OutputInterface $output)
	{
		$this->basePath = $this->handleBasePath($input, $output);
	}

	/**
	 * Read the base path from the options
	 *
	 * @param   InputInterface   $input   An InputInterface instance
	 * @param   OutputInterface  $output  An OutputInterface instance
	 *
	 * @return  string  The base path
	 */
	private function handleBasePath(InputInterface $input, OutputInterface $output)
	{
		$path = realpath($input->getOption('basepath'));

		if (!defined('JPATH_ROOT'))
		{
			define('JPATH_ROOT', $path);
		}

		$this->writeln($output, 'Joomla! installation expected in ' . $path, OutputInterface::VERBOSITY_DEBUG);

		return $path;
	}

	/**
	 * Proxy for OutputInterface::writeln()
	 *
	 * @param   OutputInterface  $output   An OutputInterface instance
	 * @param   string|array     $message  The message
	 * @param   int              $level    One of OutputInterface::VERBOSITY_*
	 * @param   int              $mode     One of OutputInterface::OUTPUT_*
	 *
	 * @return  void
	 */
	protected function writeln(OutputInterface $output, $message, $level = OutputInterface::VERBOSITY_NORMAL, $mode = OutputInterface::OUTPUT_NORMAL)
	{
		if ($output->getVerbosity() >= $level)
		{
			$output->writeln($message, $mode);
		}
	}

	/**
	 * Proxy for QuestionHelper::ask()
	 *
	 * @param   InputInterface  $input    An InputInterface instance
	 * @param   OutputInterface $output   An OutputInterface instance
	 * @param   Question        $question The question
	 *
	 * @return  string
	 */
	protected function ask(InputInterface $input, OutputInterface $output, Question $question)
	{
		$helper = new QuestionHelper;

		return $helper->ask($input, $output, $question);
	}

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
}
