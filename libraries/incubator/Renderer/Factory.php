<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Interop\Container\ContainerInterface;
use Joomla\Http\Header\AcceptHeader;
use Joomla\Renderer\Exception\NotFoundException;

/**
 * Class Factory
 *
 * @package  Joomla/Renderer
 *
 * @since    1.0
 */
class Factory
{
	/** @var array Mapping of MIME types to matching renderers */
	protected $mediaTypeMap;

	/**
	 * Factory constructor.
	 *
	 * @param   array $mapping An associative array mapping mime types to renderer classes
	 */
	public function __construct(array $mapping)
	{
		$this->mediaTypeMap = $mapping;
	}

	/**
	 * @param   string             $acceptHeader The 'Accept' header
	 * @param   ContainerInterface $container
	 *
	 * @return mixed
	 */
	public function create($acceptHeader = '*/*', ContainerInterface $container)
	{
		$header = new AcceptHeader($acceptHeader);

		$match = $header->getBestMatch(array_keys($this->mediaTypeMap));

		if (!isset($match['token']))
		{
			throw(new NotFoundException("No matching renderer found for\n\t$acceptHeader"));
		}

		$classname = $this->mediaTypeMap[$match['token']];

		return new $classname($match, $container);
	}
}
