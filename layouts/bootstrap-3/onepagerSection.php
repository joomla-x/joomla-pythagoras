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
<?php
/** @var \Joomla\Content\Type\OnePagerSection $content */
$background     = $content->getParameter('background', 'default');
$alignment      = $content->getParameter('alignment', 'left');
$height         = $content->getParameter('height', 'auto');
$sectionClass   = " class=\"bg-{$background} text-{$alignment} {$height}-height vertical-center\"";
$containerClass = $content->getParameter('class');

?>
<<?php echo $content->getType(); ?> id="<?php echo $content->getId(); ?>"<?php echo $sectionClass; ?>>
<div class="container <?php echo $containerClass; ?>">
	<?php foreach ($content->elements as $i => $element) : ?>
		<?php echo $element->html; ?>
	<?php endforeach; ?>
</div>
</<?php echo $content->getType(); ?>>
<!-- EOF <?= __FILE__ ?> -->
