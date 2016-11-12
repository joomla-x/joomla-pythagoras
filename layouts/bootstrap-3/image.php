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

if (!isset($content->params))
{
	$content->params = new stdClass;
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
	$measure = marshalMeasure($content->params->width);
	$style[] = "width: {$measure};";
}

if (!empty($content->params->height))
{
	$measure = marshalMeasure($content->params->height);
	$style[] = "height: {$measure};";
}

$inlineCSS = implode(' ', $style);

if (!empty($inlineCSS))
{
	$inlineCSS = " style=\"{$inlineCSS}\"";
}
?>
<img class="img-responsive <?php echo (isset($content->params->class)) ? $content->params->class  : ''; ?>" src="<?php echo $url; ?>" alt="<?php echo $content->alt; ?>"<?php echo $inlineCSS; ?>/>
