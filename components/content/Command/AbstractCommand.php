<?php

namespace Joomla\Component\Content\Command;

use Joomla\Command\CommandInterface;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Repository\Repository;
use Joomla\Renderer\Renderer;

abstract class AbstractCommand implements CommandInterface
{
	/** @var Repository  */
	protected $repository;

	/** @var  array */
	protected $input;

	/** @var  Renderer */
	protected $renderer;

	public function __construct($input, $renderer)
	{
		$locator = new Locator([
			new RecursiveDirectoryStrategy(dirname(__DIR__) . '/Entity'),
		]);

		$this->repository = new Repository('Article', $locator);
		$this->input      = $input;
		$this->renderer   = $renderer;
	}
}
