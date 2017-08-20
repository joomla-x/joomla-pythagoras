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
$class = $content->params->class ?? '';
?>
<div<?php echo $class ? " class=\"$class\"" : ''; ?>>
	<ul class="nav nav-tabs">
		<?php foreach ($content->elements as $i => $element) : ?>
			<?php $title = $element->content->params->title ?? 'Tab ' . $i; ?>
			<li<?php echo $i == 0 ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#<?php echo $content->id . '-' . $i; ?>"><?php echo $title; ?></a></li>
		<?php endforeach; ?>
	</ul>

	<div class="tab-content">
		<?php foreach ($content->elements as $i => $element) : ?>
			<div id="<?php echo $content->id . '-' . $i; ?>" class="tab-pane fade<?php echo $i == 0 ? ' in active' : ''; ?>">
				<?php echo $element->html; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
