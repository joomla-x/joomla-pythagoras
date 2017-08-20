<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Attribution $content
 * @codingStandardsIgnoreStart
 */

$hTag       = 'h' . $content->getParameter('hlevel', '1');
$image      = $content->article->image;
$title      = $content->article->title;
$subline    = $content->article->teaser;
$button     = '';
$url        = $content->getParameter('url', $content->url);
$buttonText = $content->getParameter('button_text', 'Read more ...');

if ($content->getParameter('show_image', true) && !empty($image))
{

	if (preg_match('~^icon:(.*+)$~', $image, $match))
	{
		$image = "<i class=\"{$match[1]}\"></i>";
	}
	else
	{
		$image = "<img src=\"/{$image}\" alt=\"{$title}\"/>";
	}

	if ($content->getParameter('link_image', true) && !empty($image))
	{
		$image = "<a href=\"{$url}\">{$image}</a>";
	}
}
else
{
	$image = '';
}

if ($content->getParameter('link_headline', false))
{
	$title = "<a href=\"{$url}\">{$title}</a>";
}

if ($content->getParameter('show_button', true))
{
	$button = "<a href=\"{$url}\" class=\"btn btn-outline btn-xl page-scroll\">{$buttonText}</a>";
}

$class = '';
if ($content->getParameter('class') > '')
{
	$class = " class=\"{$content->getParameter('class')}\"";
}
?>
<!-- <?= __FILE__ ?> -->
<div id="<?php echo $content->getId(); ?>"<?php echo $class; ?>>
	<?php echo $image; ?>
	<<?php echo $hTag; ?>><?php echo $title; ?></<?php echo $hTag; ?>>
	<p><?php echo $subline; ?></p>
	<?php echo $button; ?>
</div>
<!-- EOF <?= __FILE__ ?> -->
