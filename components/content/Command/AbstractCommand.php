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

	public function __construct()
	{
		$locator = new Locator([
			new RecursiveDirectoryStrategy(dirname(__DIR__) . '/Entity'),
		]);

		$this->repository = new Repository('Article', $locator);
	}
}
