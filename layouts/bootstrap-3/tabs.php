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
$class = $content->getParameter('class', '');
?>
<!-- <?= __FILE__ ?> -->
<div id="<?php echo $content->getId(); ?>"<?php echo $class ? " class=\"$class\"" : ''; ?>>
	<ul class="nav nav-tabs">
		<?php foreach ($content->elements as $i => $element) : ?>
			<?php $title = isset($element->getParameters()->title) ? $element->getParameters()->title : 'Tab ' . $i; ?>
			<li<?php echo $i == 0 ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#<?php echo $content->getId() . '-' . $i; ?>"><?php echo $title; ?></a></li>
		<?php endforeach; ?>
	</ul>

	<div class="tab-content">
		<?php foreach ($content->elements as $i => $element) : ?>
			<div id="<?php echo $content->getId() . '-' . $i; ?>" class="tab-pane fade<?php echo $i == 0 ? ' in active' : ''; ?>">
				<?php echo $element->html; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<!-- EOF <?= __FILE__ ?> -->
