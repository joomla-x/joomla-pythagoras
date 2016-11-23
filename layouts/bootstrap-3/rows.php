<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Tabs $content
 * @codingStandardsIgnoreStart
 */

?>
<!-- <?= __FILE__ ?> -->
<div id="<?php echo $content->getId(); ?>" class="container">
	<?php foreach ($content->elements as $i => $element) : ?>
		<?php $class = isset($element->getParameters()->class) ? $element->getParameters()->class : ''; ?>
		<div class="clearfix <?php echo $class; ?>">
			<?php echo $element->html; ?>
		</div>
	<?php endforeach; ?>
</div>
<!-- EOF <?= __FILE__ ?> -->
