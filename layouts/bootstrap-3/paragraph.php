<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Paragraph $content
 * @codingStandardsIgnoreStart
 */
?>
<?php if ($content->variant == Joomla\Content\Type\Paragraph::EMPHASISED) : ?>
	<p<?php echo (isset($content->params->class)) ? " class='{$content->params->class}'" : ""; ?>>
		<em><?php echo $content->text; ?></em>
	</p>
<?php else : ?>
	<p<?php echo (isset($content->params->class)) ? " class='{$content->params->class}'" : ""; ?>>
		<?php echo $content->text; ?>
	</p>
<?php endif; ?>
