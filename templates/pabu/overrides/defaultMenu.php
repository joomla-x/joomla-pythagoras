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
	<?php
		// @todo quickfix
		$tmp = explode('/', $menu->link);
		$anchor = $tmp[count($tmp)-1];
	?>
	<li><a href="#<?php echo $anchor; ?>"><?php echo $menu->label; ?></a>
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
	<nav <?php echo (!empty($content->params->id)) ? "id={$content->params->id}" : ""; ?> class="<?php echo $content->params->class; ?>" class="<?php echo $content->params->class; ?>">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
				</button>
				<a class="navbar-brand page-scroll" href="#page-top"><?php echo $content->item->label; ?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right" >
					<?php
					foreach ($content->item->children as $item)
					{
						call_user_func($subTree, $item, $subTree, 0, $content->params->levels - 1);
					}
					?>
				</ul>
			</div>
		</div>
	</nav>
<?php
unset($subTree);
