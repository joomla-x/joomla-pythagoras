<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Locator;

use Joomla\ORM\Definition\Locator\Strategy\StrategyInterface;

/**
 * Class Locator
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
final class Locator implements LocatorInterface
{
	/** @var  StrategyInterface[]  Locator strategies */
	private $strategies = [];

	/**
	 * Constructor
	 *
	 * @param   StrategyInterface[] $strategies The strategies used to locate files
	 */
	public function __construct(array $strategies)
	{
		$this->strategies = $strategies;
	}

	/**
	 * Find the description file for an entity
	 *
	 * @param   string $filename The name of the file
	 *
	 * @return  string  Path to the XML file
	 */
	public function findFile($filename)
	{
		$filename = basename(str_replace('\\', '/', $filename));

		foreach ($this->strategies as $strategy)
		{
			$path = $strategy->locate($filename);

			if (!is_null($path))
			{
				return $path;
			}
		}

		return null;
	}
}
