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

$url = $content->image->url;
if (!preg_match('~^(https?://|/)~', $url))
{
	$url = '/' . $url;
}
$alt = $content->alt ?? $content->image->caption;
$class = $content->params->class ?? '';
?>
<img class="img-responsive <?= $class; ?>" src="<?= $url; ?>" alt="<?= $alt; ?>"/>
