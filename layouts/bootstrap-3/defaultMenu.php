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
?>
<!-- <?= __FILE__ ?> -->
<nav id="<?php echo $content->getId(); ?>" class="<?php echo $content->getParameter('class', 'navbar navbar-inverse'); ?>">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?php echo $content->item->link; ?>"><?php echo $content->item->label; ?></a>
		</div>
		<ul class="nav navbar-nav">
			<?php
			foreach ($content->item->children as $item)
			{
				call_user_func($subTree, $item, $subTree, 0, $content->getParameter('levels', 10) - 1);
			}
			?>
		</ul>
	</div>
</nav>
<!-- EOF <?= __FILE__ ?> -->
<?php
unset($subTree);
