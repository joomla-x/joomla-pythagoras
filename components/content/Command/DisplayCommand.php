<?php
/**
 * Part of the Joomla Framework Command Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Component\Content\Command;

use Joomla\ORM\Entity\Entity;
use Joomla\Service\Command;

/**
 * Generic Display Command
 *
 * @package  Joomla/Command
 *
 * @since    1.0
 */
class DisplayCommand extends Command
{
	public function __construct($entityName, $id, $renderer)
	{
		$this->entityName = $entityName;
		$this->id = $id;
		$this->renderer = $renderer;

		parent::__construct();
	}
}
