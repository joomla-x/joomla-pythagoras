<?php
/**
 * Part of the Joomla! User Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Extension\User\Command;

use Joomla\Service\QueryHandler;
use Joomla\Cms\Service\ContentTypeQuery;
use Joomla\Content\Type\Attribution;
use Joomla\Session\SessionAwareTrait;

/**
 * User Command Handler
 *
 * @package Joomla/Extension/User
 *
 * @since 1.0
 */
class AddLoggedInUserContentTypesHandler extends QueryHandler
{
	use SessionAwareTrait;

	public function handle(ContentTypeQuery $query)
	{
		$user = $this->getSession()->get('User');

		$elements = [
				'user' => new Attribution('Logged in user ', $user ? $user->name : 'Anonymous')
		];
		$elements += $query->elements;
		return $elements;
	}
}
