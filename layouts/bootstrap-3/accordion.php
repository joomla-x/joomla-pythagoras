<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Accordion $content
 * @codingStandardsIgnoreStart
 */

$class = $content->params->class ?? '';
?>
<div class="panel-group <?= $class; ?>" id="<?= $content->id; ?>">
	<?php foreach ($content->elements as $i => $element) : ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#<?= $content->id; ?>" href="#<?= $content->id . '-' . $i; ?>">
					<?= $element->title; ?></a>
			</h4>
		</div>
		<div id="<?= $content->id . '-' . $i; ?>" class="panel-collapse collapse<?= $i == 0 ? ' in' : ''; ?>">
			<div class="panel-body">
				<?= $element->html; ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
