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
	<ul class="uk-tab" data-uk-tab data-uk-switcher="{connect:'#my-id'}">
		<?php foreach ($content->elements as $i => $element) : ?>
			<?php $title = $element->content->params->title ?? 'Tab ' . $i; ?>
			<li<?php echo $i == 0 ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#<?php echo $content->id . '-' . $i; ?>"><?php echo $title; ?></a></li>
		<?php endforeach; ?>
	</ul>

	<ul id="my-id" class="uk-switcher">
		<?php foreach ($content->elements as $i => $element) : ?>
			<li id="<?php echo $content->id . '-' . $i; ?>">
				<?php echo $element->html; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
