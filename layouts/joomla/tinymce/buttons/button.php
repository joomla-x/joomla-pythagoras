<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\Helper as JLayoutHelper;

JLog::add('The layout joomla.tinymce.buttons.button is deprecated, use joomla.editors.buttons.button instead.', JLog::WARNING, 'deprecated');
echo JLayoutHelper::render('joomla.editors.buttons.button', $displayData);

?>
