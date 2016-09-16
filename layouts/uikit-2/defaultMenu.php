<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Tree $content
 * @codingStandardsIgnoreStart
 */

if (!isset($content->params))
{
	$content->params = new stdClass;
}

if (!isset($content->params->class))
{
	$content->params->class = 'navbar navbar-inverse';
}

if (!isset($content->params->levels))
{
	$content->params->levels = 10;
}

$subTree = function($menu, $callback, $level, $maxlevel ) {
	if (empty($menu) || $level >= $maxlevel)
	{
		return;
	}

	$children = $menu->children;

	?>
	<li><a href="<?php echo $menu->link; ?>"><?php echo $menu->label; ?></a>
	<?php

	if (!empty($children))
	{
		?>
		<ul>
			<?php
			foreach ($children as $item)
			{
				call_user_func($callback, $item, $callback, $level + 1, $maxlevel);
			}
			?>
		</ul>
		<?php
	}

	?>
	</li>
	<?php
};
?>
<nav class="uk-navbar">

	<a class="uk-navbar-brand" href="<?php echo $content->item->link; ?>"><?php echo $content->item->label; ?></a>
	<ul class="uk-navbar-nav">
		<?php
		foreach ($content->item->children as $item)
		{
			call_user_func($subTree, $item, $subTree, 0, $content->params->levels - 1);	
		}
		?>
	</ul>
	<a href="" class="uk-navbar-toggle"></a>
</nav>
<?php
unset($subTree);
