<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\ORM\Storage;

use Joomla\ORM\Storage\Doctrine\DoctrineProvider;
use Joomla\ORM\Entity\EntityBuilder;

/**
 * Class DefaultProvider
 *
 * The default provider is reading the connectio information from a global
 * config file.
 *
 * @package Joomla/ORM
 *
 * @since 1.0
 */
class DefaultProvider extends DoctrineProvider
{

	public function __construct(array $parameters, EntityBuilder $builder)
	{
		$parameters = array_merge($parameters, parse_ini_file('config/database.ini'));
		parent::__construct($parameters, $builder);
	}
}
