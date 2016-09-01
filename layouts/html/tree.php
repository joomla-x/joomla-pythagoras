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

$subTree = function($node, $callback) {
	if (empty($node))
	{
		return;
	}

	$children = isset($node->children) ? $node->children->getAll() : null;

	if (empty($children))
	{
		?>
		<li><a href="#"><?= $node->title; ?></a></li>
		<?php
	}
	else
	{
		?>
		<li><label class="tree-toggler nav-header"><a href="#"><?= $node->title; ?></a></label>
			<ul class="nav nav-list tree" style="display: none;">
				<?php
				foreach ($children as $item)
				{
					call_user_func($callback, $item, $callback);
				}
				?>
			</ul>
		</li>
		<?php
	}
};

$class = $content->params->class ?? '';
?>
<div<?= $class ? " class=\"$class\"" : ''; ?>>
	<ul class="nav nav-list tree">
		<?= call_user_func($subTree, $content->item, $subTree); ?>
	</ul>
</div>
<?php
unset($subTree);

$js = <<<JS
$(document).ready(function () {
	$('label.tree-toggler').click(function () {
		$(this).parent().children('ul.tree').toggle(300);
	});
});
JS;

$this->addJavascript('tree', $js);
