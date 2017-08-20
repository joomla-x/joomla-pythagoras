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
<span id="<?php echo $content->getId(); ?>"<?php echo (isset($content->getParameters()->class)) ? " class='{$content->getParameter()->class}'" : ""; ?>>
	<?php echo $content->text; ?>
</span>
<!-- EOF <?= __FILE__ ?> -->
