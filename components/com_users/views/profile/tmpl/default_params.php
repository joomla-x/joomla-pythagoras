<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Html\Html as JHtml;

JLoader::register('UsersHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('usersHtml.users.spacer', array('UsersHtmlUsers', 'spacer'));
JHtml::register('usersHtml.users.helpsite', array('UsersHtmlUsers', 'helpsite'));
JHtml::register('usersHtml.users.templatestyle', array('UsersHtmlUsers', 'templatestyle'));
JHtml::register('usersHtml.users.admin_language', array('UsersHtmlUsers', 'admin_language'));
JHtml::register('usersHtml.users.language', array('UsersHtmlUsers', 'language'));
JHtml::register('usersHtml.users.editor', array('UsersHtmlUsers', 'editor'));

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
<fieldset id="users-profile-custom">
	<legend><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
	<dl class="dl-horizontal">
	<?php foreach ($fields as $field):
		if (!$field->hidden) :?>
		<dt><?php echo $field->title; ?></dt>
		<dd>
			<?php if (JHtml::isRegistered('usersHtml.users.' . $field->id)):?>
				<?php echo JHtml::_('usersHtml.users.' . $field->id, $field->value);?>
			<?php elseif (JHtml::isRegistered('usersHtml.users.' . $field->fieldname)):?>
				<?php echo JHtml::_('usersHtml.users.' . $field->fieldname, $field->value);?>
			<?php elseif (JHtml::isRegistered('usersHtml.users.' . $field->type)):?>
				<?php echo JHtml::_('usersHtml.users.' . $field->type, $field->value);?>
			<?php else:?>
				<?php echo JHtml::_('usersHtml.users.value', $field->value);?>
			<?php endif;?>
		</dd>
		<?php endif;?>
	<?php endforeach;?>
	</dl>
</fieldset>
<?php endif;?>
