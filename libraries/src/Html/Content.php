<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Html;

defined('JPATH_PLATFORM') or die;

use JFactory;
use JObject;
use Joomla\CMS\Plugin\Helper as JPluginHelper;
use stdClass;

/**
 * Utility class to fire onContentPrepare for non-article based content.
 *
 * @since  1.5
 */
abstract class Content
{
	/**
	 * Fire onContentPrepare for content that isn't part of an article.
	 *
	 * @param   string  $text     The content to be transformed.
	 * @param   array   $params   The content params.
	 * @param   string  $context  The context of the content to be transformed.
	 *
	 * @return  string   The content after transformation.
	 *
	 * @since   1.5
	 */
	public static function prepare($text, $params = null, $context = 'text')
	{
		if ($params === null)
		{
			$params = new JObject;
		}

		$article = new stdClass;
		$article->text = $text;
		JPluginHelper::importPlugin('content');
		JFactory::getApplication()->triggerEvent('onContentPrepare', array($context, &$article, &$params, 0));

		return $article->text;
	}
}
