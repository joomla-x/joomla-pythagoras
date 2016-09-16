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
<div class="container">
	<?php foreach ($content->elements as $i => $element) : ?>
		<?php $class = $element->params->class ?? ''; ?>
		<div class="clearfix <?php echo $class; ?>">
			<?php echo $element->html; ?>
		</div>
	<?php endforeach; ?>
</div>
