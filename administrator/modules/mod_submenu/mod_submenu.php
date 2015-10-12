<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_submenu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$list    = JHtml::_('sidebar.getEntries');
$filters = JHtml::_('sidebar.getFilters');
$action  = JHtml::_('sidebar.getAction');

$displayMenu    = count($list);
$displayFilters = count($filters);

$hide = JFactory::getApplication()->input->getBool('hidemainmenu');

if ($displayMenu || $displayFilters)
{
	require JModuleHelper::getLayoutPath('mod_submenu', $params->get('layout', 'default'));
}
