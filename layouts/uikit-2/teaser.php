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
<section>
	<header>
		<h1><?php echo $content->article->title; ?></h1>
		<p>
			<small>Written by <?php echo $content->article->author; ?></small>
		</p>
	</header>
	<main>
		<?php echo $content->article->teaser; ?>
	</main>
	<footer>
		<p>
			<a href="<?php echo $content->url; ?>">Read more ...</a>
		</p>
	</footer>
</section>
