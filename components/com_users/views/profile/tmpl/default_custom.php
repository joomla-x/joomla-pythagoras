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

$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

foreach ($fieldsets as $group => $fieldset): // Iterate through the form fieldsets
	$fields = $this->form->getFieldset($group);
	if (count($fields)):
?>

<fieldset id="users-profile-custom" class="users-profile-custom-<?php echo $group; ?>">
	<?php // If the fieldset has a label set, display it as the legend. ?>
	<?php if (isset($fieldset->label)): ?>
	<legend><?php echo JText::_($fieldset->label); ?></legend>
	<?php endif; ?>
	<dl class="dl-horizontal">
	<?php foreach ($fields as $field) :
		if (!$field->hidden && $field->type != 'Spacer') : ?>
		<dt><?php echo $field->title; ?></dt>
		<dd>
			<?php if (JHtml::isRegistered('usersHtml.users.' . $field->id)) : ?>
				<?php echo JHtml::_('usersHtml.users.' . $field->id, $field->value); ?>
			<?php elseif (JHtml::isRegistered('usersHtml.users.' . $field->fieldname)) : ?>
				<?php echo JHtml::_('usersHtml.users.' . $field->fieldname, $field->value); ?>
			<?php elseif (JHtml::isRegistered('usersHtml.users.' . $field->type)) : ?>
				<?php echo JHtml::_('usersHtml.users.' . $field->type, $field->value); ?>
			<?php else : ?>
				<?php echo JHtml::_('usersHtml.users.value', $field->value); ?>
			<?php endif; ?>
		</dd>
		<?php endif; ?>
	<?php endforeach; ?>
	</dl>
</fieldset>
	<?php endif; ?>
<?php endforeach; ?>
