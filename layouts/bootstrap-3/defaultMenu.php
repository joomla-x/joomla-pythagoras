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

$class = $content->params->class ?? '';
$level = $content->params->levels ?? 10;
?>
<nav<?php echo $class ? " class=\"$class\"" : ''; ?>>
	<ul class="nav nav-pills nav-stacked">
		<?php echo call_user_func($subTree, $content->item, $subTree, 0, $level); ?>
	</ul>
</nav>
<?php
unset($subTree);
