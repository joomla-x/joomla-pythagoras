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

if (!isset($content->params))
{
	$content->params = new stdClass;
}

if (!isset($content->params->class))
{
	$content->params->class = '';
}

if (!isset($content->params->width))
{
	$content->params->width = $content->image->width;
}

if (!isset($content->params->height))
{
	$content->params->height = $content->image->height;
}

$url = $content->image->url;
if (!preg_match('~^(https?://|/)~', $url))
{
	$url = '/' . $url;
}

if (!isset($content->alt))
{
	$content->alt = $content->image->caption;
}

$style = [];

if (!empty($content->params->width))
{
	$style[] = "width: {$content->params->width};";
}

if (!empty($content->params->height))
{
	$style[] = "height: {$content->params->height};";
}

$inlineCSS = implode(' ', $style);

if (!empty($inlineCSS))
{
	$inlineCSS = " style=\"{$inlineCSS}\"";
}
?>
<img class="img-responsive <?php echo $content->params->class; ?>" src="<?php echo $url; ?>" alt="<?php echo $content->alt; ?>"<?php echo $inlineCSS; ?>/>
