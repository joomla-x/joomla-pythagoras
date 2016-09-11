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
<article>
	<header>
		<h1><?= $content->article->title; ?></h1>
		<p>
			<small>Written by <?= $content->article->author; ?></small>
		</p>
	</header>
	<main>
		<?= $content->article->body; ?>
	</main>
	<footer>
		<p><small>License: <?= $content->article->license; ?></small></p>
	</footer>
</article>
