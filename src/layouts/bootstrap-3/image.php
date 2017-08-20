<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Headline $content
 * @codingStandardsIgnoreStart
 */

require_once dirname(__DIR__) . '/functions.php';

$url = $content->image->url;

if (!preg_match('~^(https?://|/)~', $url))
{
	$url = '/' . $url;
}

if (empty($content->alt))
{
	$content->alt = $content->image->caption;
}

$style = [];

if (!empty($content->getParameters()->width))
{
	$measure = marshalMeasure($content->getParameters()->width);
	$style[] = "width: {$measure};";
}

if (!empty($content->getParameters()->height))
{
	$measure = marshalMeasure($content->getParameters()->height);
	$style[] = "height: {$measure};";
}

if (!empty($inlineCSS))
{
	$this->addCss($content->getId(), 'img {' . implode(' ', $style) . '}');
}
?>
<!-- <?= __FILE__ ?> -->
<figure id="<?php echo $content->getId(); ?>">
	<img class="img-responsive <?php echo $content->getParameter('class', ''); ?>"
	     src="<?php echo $url; ?>" alt="<?php echo $content->alt; ?>">
	<?php if ($content->getParameter('show_caption', false)) : ?>
		<figcaption>
			<h2><?php echo $content->image->caption; ?></h2>
			<?php if ($content->getParameter('show_description', false)) : ?>
				<p><?php echo $content->image->description; ?></p>
			<?php endif; ?>
		</figcaption>
	<?php endif; ?>
</figure>
<!-- EOF <?= __FILE__ ?> -->
