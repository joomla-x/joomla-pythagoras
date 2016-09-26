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

$subTree = function($node, $callback, $level = 0) {
	if (empty($node))
	{
		return;
	}

	$children = isset($node->children) ? $node->children->getAll() : null;

	if (empty($children))
	{
		?>
		<li><a href="#"><?php echo $node->title; ?></a></li>
		<?php
	}
	else
	{
		?>
		<li class="uk-parent">
			<?php if ($level == 0) : ?>
			<a href="#"><?php echo $node->title; ?></a>
			<?php else : ?>
			<?php endif; ?>
				<ul class="uk-nav-sub">
				<?php
				foreach ($children as $item)
				{
					call_user_func($callback, $item, $callback, $level + 1);
				}
				?>
			</ul>
		</li>
		<?php
	}
};

$class = $content->params->class ?? '';
?>
<div<?php echo $class ? " class=\"$class\"" : ''; ?>>
	<ul class="uk-nav uk-nav-parent-icon">
		<?php echo call_user_func($subTree, $content->item, $subTree); ?>
	</ul>
</div>
<?php
unset($subTree);


