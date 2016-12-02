<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * ---------------------
 * 	$options         : (array)  List of options
 * 	$id           	 : (string) id for the select field
 * 	$name            : (string) The name for the select field
 */

extract($displayData);
?>

<select 
	<?php if (!empty($id)) : ?>
	id="<?php echo $id; ?>" 
	<?php endif;  ?>
	<?php if (!empty($name)) : ?>
	name="<?php echo $name; ?>"
	<?php endif;  ?>
	<?php if (!empty($class)) : ?>
	class="<?php $class; ?>"
	<?php endif;  ?>
	<?php if (!empty($size)) : ?>
	size="<?php $size; ?>"
	<?php endif;  ?>
	<?php if ($multiple) : ?>
	multiple
	<?php endif;  ?>
	<?php if ($required) : ?>
	required aria-required="true"
	<?php endif;  ?>
	<?php if ($required) : ?>
	autofocus
	<?php endif;  ?>
	<?php 
	// To avoid user's confusion, readonly="true" should imply disabled="true".
	if ((string) $readonly == '1' || (string) $readonly == 'true' || (string) $disabled == '1'|| (string) $disabled == 'true') :
	?>
	disabled="disabled"
	<?php endif;  ?>
	<?php if ($onchange) : ?>
	onchange="<?php echo $onchange; ?>"
	<?php endif;  ?>
>
	<?php foreach ($options as $option) : ?>
		<option 
			value="<?php echo $option->value; ?>"
			<?php if ($option->disable) : ?>
			disabled="disabled"
			<?php endif; ?>
			<?php if ($option->class) : ?>
			class="<?php echo $option->class; ?>"
			<?php endif; ?>
			<?php if ($option->selected) : ?>
			selected="selected"
			<?php endif; ?>
			<?php if ($option->checked) : ?>
			checked="checked"
			<?php endif; ?>
			<?php if ($option->onchange) : ?>
			onchange="<?php echo $option->onchange; ?>"
			<?php endif;  ?>
			<?php if ($option->onclick) : ?>
			onclick="<?php echo $option->onclick; ?>"
			<?php endif;  ?>
		>
			<?php echo $option->text; ?>
		</option>
	<?php endforeach; ?>
</select>

<?php 
// E.g. form field type tag sends $this->value as array
if ($multiple && is_array($value)) : ?>
	<?php foreach ($value as $v) : ?>
		<input type="hidden" name="<?php echo $hiddenName; ?>" value="<?php echo htmlspecialchars($v, ENT_COMPAT, 'UTF-8'); ?>"/>
	<?php endforeach; ?>
<?php else : ?>
	<input type="hidden" name="<?php echo $hiddenName; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"/>
<?php endif; ?>
