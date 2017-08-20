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

$class = $content->getParameter('class', '');
?>
<!-- <?= __FILE__ ?> -->
<div class="panel-group <?php echo $class; ?>" id="<?php echo $content->getId(); ?>">
	<?php foreach ($content->elements as $i => $element) : ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#<?php echo $content->getId(); ?>" href="#<?php echo $content->getId() . '-' . $i; ?>">
					<?php echo $element->getTitle(); ?></a>
			</h4>
		</div>
		<div id="<?php echo $content->getId() . '-' . $i; ?>" class="panel-collapse collapse<?php echo $i == 0 ? ' in' : ''; ?>">
			<div class="panel-body">
				<?php echo $element->html; ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<!-- EOF <?= __FILE__ ?> -->
