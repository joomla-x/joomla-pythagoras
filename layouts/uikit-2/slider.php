<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Slider $content
 *
 * @codingStandardsIgnoreStart
 */

if (!isset($content->params))
{
	$content->params = new stdClass;
}

if (!isset($content->params->class))
{
	$content->params->class = 'slide';
}
?>

<div id="<?php echo $content->id; ?>" class="uk-slidenav-position <?php echo $content->params->class; ?>" data-uk-slideshow>
	
	<!-- Wrapper for slides -->
	<ul class="uk-slideshow">
		<?php foreach ($content->elements as $i => $element) : ?>
		<li class="item<?php echo $i == 0 ? ' active' : ''; ?>">
			<?php echo $element->html; ?>
		</li>
		<?php endforeach; ?>
	</ul>

	<!-- Left and right controls -->
	<a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" data-uk-slideshow-item="previous"></a>
    	<a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" data-uk-slideshow-item="next"></a>
    
	<ul class="uk-dotnav uk-dotnav-contrast uk-position-bottom uk-flex-center">
    	<?php for ($i=0, $n = count($content->elements); $i < $n; ++$i) : ?>
        	<li data-uk-slideshow-item="<?php echo $i; ?>"><a href=""></a></li>
        <?php endfor; ?>
	</ul>
</div>
