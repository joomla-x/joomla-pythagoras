<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The Joomla document service provider.
 *
 * @since  4.0
 */
class DocumentProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		// Setting the callables for the document
		$container->set('JDocument', array($this, 'getDocument'));
	}

	/**
	 * Returns a document from the language and the requested format
	 * in the container.
	 *
	 * @param Container $container
	 *
	 * @return JDocument
	 */
	public function getDocument(Container $container)
	{
		$lang = $container->get('language');

		$input = $container->get('input');
		$type = $input->get('format', 'html', 'word');

		$version = new \JVersion;

		$attributes = array(
			'charset' => 'utf-8',
			'lineend' => 'unix',
			'tab' => '  ',
			'language' => $lang->getTag(),
			'direction' => $lang->isRtl() ? 'rtl' : 'ltr',
			'mediaversion' => $version->getMediaVersion($container->get('config'))
		);

		return \JDocument::getInstance($type, $attributes);
	}
}