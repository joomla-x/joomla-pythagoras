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
		<li>
			<?php if ($level == 0) : ?>
			<label class="tree-toggler nav-header"><a href="#"><?php echo $node->title; ?></a></label>
			<?php else : ?>
			<a href="#" class="tree-toggler"><?php echo $node->title; ?></a>
			<?php endif; ?>
				<ul class="nav nav-list tree" style="display: none;">
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

$class = $content->getParameter('class', '');
?>
<!-- <?= __FILE__ ?> -->
<div id="<?php echo $content->getId(); ?>"<?php echo $class ? " class=\"$class\"" : ''; ?>>
	<ul class="nav nav-list tree">
		<?php echo call_user_func($subTree, $content->item, $subTree); ?>
	</ul>
</div>
	<!-- EOF <?= __FILE__ ?> -->
<?php
unset($subTree);

$js = <<<JS
$(document).ready(function () {
	$('.tree-toggler').click(function () {
		$(this).parent().children('ul.tree').toggle(300);
	});
});
JS;

$this->addJavascript('.tree-toggler', $js);
