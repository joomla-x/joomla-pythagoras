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
<div class="uk-accordion  <?php echo $class; ?>"  id="<?php echo $content->id; ?>" data-uk-accordion>
	<?php foreach ($content->elements as $i => $element) : ?>
		<h3 class="uk-accordion-title"><?php echo $element->title; ?></h3>
    		<div class="uk-accordion-content"><?php echo $element->html; ?></div>
	<?php endforeach; ?>
</div>
