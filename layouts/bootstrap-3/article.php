<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Attribution $content
 * @codingStandardsIgnoreStart
 */

?>
<!-- <?= __FILE__ ?> -->
<article id="<?php echo $content->getId(); ?>">
	<header>
		<h1><?php echo $content->article->title; ?></h1>
		<p>
			<small>Written by <?php echo $content->article->author; ?></small>
		</p>
	</header>
	<main>
		<?php echo $content->article->body; ?>
	</main>
	<footer>
		<p><small>License: <?php echo $content->article->license; ?></small></p>
	</footer>
</article>
<!-- EOF <?= __FILE__ ?> -->
