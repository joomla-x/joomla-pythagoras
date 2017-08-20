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
<!-- <?= __FILE__ ?> -->
<?php $class = $content->getParameter('class'); ?>
<?php if ($content->variant == Joomla\Content\Type\Paragraph::EMPHASISED) : ?>
	<p id="<?php echo $content->getId(); ?>"<?php echo (!empty($class)) ? " class='{$class}'" : ""; ?>>
		<em><?php echo $content->text; ?></em>
	</p>
<?php else : ?>
	<p id="<?php echo $content->getId(); ?>"<?php echo (!empty($class)) ? " class='{$class}'" : ""; ?>>
		<?php echo $content->text; ?>
	</p>
<?php endif; ?>
<!-- EOF <?= __FILE__ ?> -->
